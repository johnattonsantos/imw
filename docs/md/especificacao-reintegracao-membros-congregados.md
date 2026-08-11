# Especificação --- Reintegração de Membros e Congregados

## 1. Objetivo

Implementar uma nova funcionalidade para permitir a **reintegração de
pessoas já cadastradas no sistema**, permitindo que uma pessoa
anteriormente excluída/inativada seja recebida novamente como
**Congregado** ou **Membro** na igreja atualmente logada.

A funcionalidade deverá preservar o histórico anterior da pessoa e
atualizar o cadastro atual conforme o novo vínculo.

------------------------------------------------------------------------

## 2. Nova opção no menu

Adicionar uma nova opção no menu **Secretaria**, imediatamente abaixo de
**Visitantes**:

> **Reintegração de membros e congregados**

Ao acessar a funcionalidade, a tela deverá inicialmente apresentar
**somente o campo CPF**.

**CPF:** `XXX.XXX.XXX-XX`

Após informar o CPF, o sistema deverá consultar o cadastro existente e
determinar a situação da pessoa.

------------------------------------------------------------------------

## 3. Regras para o CPF informado

### 3.1. CPF não cadastrado

Caso o CPF **não exista no sistema**, informar:

> **Pessoa não cadastrada no sistema. Para cadastrar esta pessoa,
> utilize a função Congregados → Incluir congregado.**

Não permitir o prosseguimento pela funcionalidade de reintegração.

------------------------------------------------------------------------

### 3.2. Pessoa é membro ativo em outra igreja

Caso o CPF exista e a pessoa esteja registrada como **membro ativo em
uma igreja diferente da igreja atualmente logada**, exibir:

> **"\[Nome da pessoa\]" é membro ativo na igreja \[Igreja\], Distrito
> \[Distrito\], \[X\]ª Região. Para proceder com a inclusão, a igreja de
> origem deve iniciar o procedimento de transferência, para ser aceito
> na igreja atual.**

Não permitir a reintegração enquanto a pessoa estiver ativa em outra
igreja.

A igreja atual deve ser determinada pelo contexto da sessão/usuário
logado.

------------------------------------------------------------------------

### 3.3. Pessoa é membro inativo em outra igreja

Caso o CPF exista e a pessoa esteja registrada como **membro inativo em
outra igreja**, apresentar os dados para reintegração.

Exibir:

**Nome:** `[nome cadastrado]`

-   Campo somente leitura.
-   O nome deve ser obtido do cadastro existente.
-   Não permitir alteração do nome nesta funcionalidade.

Em seguida, apresentar a opção:

**Como a pessoa será recebida?**

-   `( ) Congregado`
-   `( ) Membro`

A escolha determinará o fluxo seguinte.

------------------------------------------------------------------------

# 4. Reintegração como Congregado

Quando o usuário selecionar **Congregado**, não apresentar campos
adicionais.

Ao confirmar a operação, atualizar o registro existente em
`membresia_membros`.

## 4.1. Campos a atualizar

  Campo           Valor
  --------------- --------------------------------------
  `status`        `A`
  `vinculo`       `C`
  `rol`           `NULL`
  `igreja_id`     Igreja atualmente logada
  `distrito_id`   Distrito da igreja atualmente logada
  `regiao_id`     Região da igreja atualmente logada
  `update_at`     Data/hora atual

## 4.2. `membresia_rolpermanente`

**Não criar nem alterar nenhum registro em `membresia_rolpermanente`.**

O histórico anterior da pessoa deve ser preservado.

Somente o cadastro atual em `membresia_membros` deverá ser atualizado.

------------------------------------------------------------------------

# 5. Reintegração como Membro

Quando o usuário selecionar **Membro**, apresentar os seguintes campos
adicionais.

## 5.1. Número do rol

Exibir automaticamente o **próximo número de rol disponível para a
igreja atual**:

> `MAX(rol da igreja atual) + 1`

O campo deverá ser **editável**, permitindo que o usuário informe outro
número quando necessário.

A numeração deverá considerar somente os membros/rols da **igreja
atualmente logada**.

------------------------------------------------------------------------

## 5.2. Data de recepção

Campo obrigatório.

### Regras de validação

A data:

-   não pode ser maior que a data atual;
-   não pode ser inferior à data de exclusão da igreja anterior.

Portanto:

`data_exclusao_anterior <= data_recepcao <= data_atual`

Caso a data informada seja inválida, impedir a conclusão da operação e
informar o motivo ao usuário.

------------------------------------------------------------------------

## 5.3. Modo de recepção

Campo obrigatório.

Apresentar a lista de opções de **modo de recepção** já utilizada pelo
sistema.

Deve ser utilizada a mesma origem/cadastro de opções existente na
funcionalidade atual de inclusão/reintegração de membros, evitando a
criação de uma nova lista independente.

------------------------------------------------------------------------

## 5.4. Congregação

Apresentar uma lista contendo as congregações pertencentes à **igreja
atualmente logada**.

A seleção é opcional.

Caso nenhuma congregação seja selecionada:

``` text
congregacao_id = NULL
```

Nesse caso, considerar que o membro pertence à **sede**.

------------------------------------------------------------------------

# 6. Persistência da reintegração como membro

Ao confirmar a operação como **Membro**, utilizar a função existente de
**reintegrar membro**, centralizando nela as regras de atualização.

A implementação deverá evitar duplicação da lógica de negócio entre
controller, tela e service.

## 6.1. Atualização de `membresia_membros`

Atualizar o cadastro existente da pessoa com os dados correspondentes à
nova situação:

-   `status = A`;
-   `vinculo = M`;
-   novo número de rol;
-   igreja atual;
-   distrito atual;
-   região atual;
-   congregação selecionada, ou `NULL`;
-   demais informações necessárias ao processo de reintegração;
-   `update_at` atualizado.

## 6.2. Criação em `membresia_rolpermanente`

Criar um **novo registro histórico** correspondente à nova recepção.

O novo registro deverá:

-   utilizar `lastrec = 1`;
-   utilizar o número de rol informado;
-   utilizar a data de recepção informada;
-   utilizar o modo de recepção informado;
-   utilizar a igreja atual;
-   utilizar o distrito atual;
-   utilizar a região atual;
-   utilizar a congregação informada, ou `NULL`;
-   utilizar os demais dados pertinentes ao processo.

O registro anterior de `membresia_rolpermanente` **não deve ser
sobrescrito**, pois representa o histórico anterior da pessoa.

------------------------------------------------------------------------

# 7. Alteração em Congregados → Incluir congregado

A funcionalidade **Congregados → Incluir congregado** deverá voltar a
funcionar como funcionava anteriormente, com uma exceção para pessoas
que já possuem cadastro no sistema.

Ao informar um CPF que **já existe no sistema**, não permitir a inclusão
como novo congregado.

Informar:

> **Esta pessoa já existe no sistema. Para recebê-la novamente, utilize
> a opção "Reintegração de membros e congregados".**

A operação de inclusão deverá ser interrompida.

## 7.1. Regra

A função **Congregados → Incluir congregado** deverá ser utilizada para
pessoas cujo CPF **ainda não esteja cadastrado no sistema**.

Pessoas já cadastradas deverão passar pelo fluxo de **Reintegração de
membros e congregados**.

------------------------------------------------------------------------

# 8. Fluxo resumido

``` text
Secretaria
   └── Reintegração de membros e congregados
          │
          └── Informar CPF
                 │
                 ├── CPF não cadastrado
                 │      └── Informar:
                 │          "Pessoa não cadastrada..."
                 │
                 ├── Membro ativo em outra igreja
                 │      └── Informar igreja/distrito/região
                 │          e orientar transferência
                 │
                 └── Membro inativo
                        │
                        ├── Congregado
                        │      └── Atualiza membresia_membros
                        │          ├── status = A
                        │          ├── vinculo = C
                        │          ├── rol = NULL
                        │          ├── igreja atual
                        │          ├── distrito atual
                        │          ├── região atual
                        │          └── NÃO altera rolpermanente
                        │
                        └── Membro
                               ├── Número do rol
                               ├── Data de recepção
                               ├── Modo de recepção
                               └── Congregação (opcional)
                                      │
                                      └── Reintegrar membro
                                             ├── Atualiza
                                             │   membresia_membros
                                             │
                                             └── Cria novo
                                                 membresia_rolpermanente
                                                 lastrec = 1
```

------------------------------------------------------------------------

# 9. Regras importantes para implementação

1.  **Não criar uma nova pessoa** durante a reintegração. O CPF já
    existe; deve ser reutilizado o registro existente em
    `membresia_membros`.

2.  **Reintegração como Congregado não cria nem altera
    `membresia_rolpermanente`.**

3.  **Reintegração como Membro cria um novo registro em
    `membresia_rolpermanente`**, preservando o histórico anterior.

4.  Igreja, distrito e região devem ser obtidos **do contexto da igreja
    logada**, e não informados manualmente pelo usuário.

5.  A validação de membro ativo deve impedir a operação quando a igreja
    em que a pessoa está ativa for diferente da igreja atual.

6.  A validação da data de recepção deve utilizar a **data de exclusão
    registrada para a igreja anterior**.

7.  O número automático do rol é apenas uma sugestão: **o usuário pode
    alterá-lo**.

8.  `congregacao_id = NULL` é válido e representa membro da sede.

9.  A lógica de reintegração como membro deve ficar em uma
    **função/service de domínio reutilizável**, em vez de implementar
    toda a regra diretamente no controller da nova tela.

10. A operação de reintegração como membro deve ser **transacional**. A
    atualização de `membresia_membros` e a criação do novo
    `membresia_rolpermanente` devem ocorrer como uma única operação. Se
    uma das etapas falhar, nenhuma das alterações deverá ser persistida.

11. O histórico existente em `membresia_rolpermanente` não deve ser
    excluído ou sobrescrito pela reintegração.

12. A funcionalidade **Congregados → Incluir congregado** deve continuar
    permitindo o cadastro de novos CPFs, mas deve bloquear CPFs que já
    estejam cadastrados no sistema.

------------------------------------------------------------------------

# 10. Critérios de aceite

### CPF não cadastrado

-   [ ] Informar CPF inexistente.
-   [ ] Sistema informa que a pessoa não está cadastrada.
-   [ ] Sistema orienta o uso de **Congregados → Incluir congregado**.
-   [ ] Não é possível prosseguir na reintegração.

### Membro ativo em outra igreja

-   [ ] Informar CPF de membro ativo em outra igreja.
-   [ ] Sistema apresenta nome da pessoa.
-   [ ] Sistema apresenta igreja, distrito e região atuais.
-   [ ] Sistema orienta que a igreja de origem deve iniciar a
    transferência.
-   [ ] Não é possível prosseguir na reintegração.

### Reintegração como Congregado

-   [ ] Informar CPF de membro inativo.
-   [ ] Sistema apresenta o nome cadastrado, sem possibilidade de
    edição.
-   [ ] Selecionar Congregado.
-   [ ] Nenhum campo adicional é apresentado.
-   [ ] `membresia_membros` é atualizado corretamente.
-   [ ] `status = A`.
-   [ ] `vinculo = C`.
-   [ ] `rol = NULL`.
-   [ ] Igreja, distrito e região correspondem à igreja logada.
-   [ ] `update_at` é atualizado.
-   [ ] Nenhum registro de `membresia_rolpermanente` é criado ou
    alterado.

### Reintegração como Membro

-   [ ] Informar CPF de membro inativo.
-   [ ] Selecionar Membro.
-   [ ] Campos de número do rol, data de recepção, modo de recepção e
    congregação são apresentados.
-   [ ] Número do rol é preenchido inicialmente com o próximo número
    disponível.
-   [ ] Número do rol pode ser editado.
-   [ ] Data futura é rejeitada.
-   [ ] Data anterior à data de exclusão da igreja anterior é rejeitada.
-   [ ] Modo de recepção é obrigatório.
-   [ ] Congregação é opcional.
-   [ ] Ausência de congregação resulta em `congregacao_id = NULL`.
-   [ ] `membresia_membros` é atualizado.
-   [ ] Novo registro é criado em `membresia_rolpermanente`.
-   [ ] Novo registro possui `lastrec = 1`.
-   [ ] Registro histórico anterior é preservado.
-   [ ] Toda a operação ocorre de forma transacional.

### Inclusão de congregado

-   [ ] CPF inexistente continua podendo ser incluído normalmente.
-   [ ] CPF já existente não pode ser incluído novamente.
-   [ ] Sistema informa que a pessoa já existe.
-   [ ] Sistema orienta o uso da funcionalidade de **Reintegração de
    membros e congregados**.
