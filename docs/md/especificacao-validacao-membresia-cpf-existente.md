# Especificação — Tratamento de CPF já existente na Validação de Membresia

## 1. Objetivo

Alterar a funcionalidade **Membresia → Validação** para tratar corretamente situações em que o CPF informado já esteja cadastrado na tabela `membresia_membros`.

A validação deve ocorrer **no momento do salvamento do registro**, juntamente com a crítica de CPF que já existe atualmente.

### Regra importante

A verificação de CPF para esta funcionalidade deve ser feita **exclusivamente na tabela `membresia_membros`**.

> **Não realizar nenhuma validação ou consulta em `membresia_migracao` para determinar se o CPF já existe.**

---

# 2. Membro sendo incluído como ATIVO

Quando o registro que está sendo incluído possuir status **ATIVO**, verificar se o CPF já existe em `membresia_membros`.

A ação dependerá da situação do registro encontrado.

---

## 2.1. CPF pertence a membro INATIVO em outra igreja

Se o CPF já existir em `membresia_membros` como **membro inativo** e estiver vinculado a uma igreja diferente da igreja atual:

Exibir uma confirmação ao usuário:

> **Este CPF pertence a [NOME], que está registrado como membro INATIVO na igreja [IGREJA], distrito [DISTRITO], [X]ª região. Deseja continuar com a inclusão?**

### Se o usuário responder NÃO

- Cancelar o salvamento.
- Não realizar nenhuma alteração no banco de dados.

### Se o usuário responder SIM

Não criar um novo registro de pessoa/membro.

Utilizar o `id` do registro de `membresia_membros` encontrado e:

1. Atualizar o registro existente com os dados da nova inclusão.
2. Vincular o membro à igreja atualmente logada.
3. Atualizar os demais dados de membresia conforme os dados informados na validação.
4. Criar um **novo registro em `membresia_rolpermanente`**, representando a nova entrada no rol.
5. Preservar o histórico anterior.

A operação deve ser realizada de forma transacional.

### Regra de implementação

Sempre que possível, utilizar a mesma lógica/service de **reintegração de membro** já existente ou definida para a funcionalidade **Reintegração de membros e congregados**, evitando duplicação de regras de negócio.

---

## 2.2. CPF já pertence à própria igreja

Se o CPF já existir em `membresia_membros` e o registro estiver vinculado à **igreja atualmente logada**, informar:

> **Este CPF pertence a [NOME], que já está registrado nesta igreja. Caso necessário, utilize a função de "Reintegração" para trazer o membro de volta ao rol ativo.**

### Ação

- Não permitir a criação de um novo registro.
- Não alterar o registro existente.
- Interromper o salvamento.

---

## 2.3. CPF pertence a membro ATIVO em outra igreja

Se o CPF já existir em `membresia_membros` como **membro ativo**, vinculado a outra igreja:

Exibir:

> **Este CPF pertence a [NOME], que está registrado como membro ATIVO na igreja [IGREJA], distrito [DISTRITO], [X]ª região. Caso este tenha se transferido para esta igreja, entre em contato com a igreja de origem para que seja iniciado o processo de transferência, ou entre em contato com o suporte do IMWPlus para obter mais informações.**

### Ação

- Não permitir a inclusão.
- Não alterar o registro existente.
- Não criar registro em `membresia_rolpermanente`.
- Interromper o salvamento.

O processo correto nesse cenário é a **transferência** iniciada pela igreja de origem.

---

# 3. Membro sendo incluído como INATIVO

Quando o registro que está sendo incluído possuir status **INATIVO**, verificar se o CPF já existe em `membresia_membros`.

Caso exista, **não permitir a inclusão de um novo registro**.

Exibir:

> **Este CPF pertence a [NOME], que está registrado como membro na igreja [IGREJA], portanto não há como adicioná-lo no sistema.**

### Ação

- Não permitir a inclusão.
- Não alterar o registro existente.
- Não criar registro em `membresia_rolpermanente`.
- Interromper o salvamento.

### Regra

Para inclusão como **INATIVO**, qualquer ocorrência do CPF em `membresia_membros` deve bloquear a operação, independentemente do status do registro existente.

---

# 4. Regra geral de decisão

```text
Salvar validação de membresia
        │
        ├── CPF não existe em membresia_membros
        │       └── Continua fluxo atual de inclusão
        │
        └── CPF existe em membresia_membros
                │
                ├── Inclusão = ATIVO
                │       │
                │       ├── Existente = INATIVO
                │       │      │
                │       │      ├── Outra igreja
                │       │      │      └── Perguntar confirmação
                │       │      │             │
                │       │      │             ├── NÃO → cancelar
                │       │      │             │
                │       │      │             └── SIM
                │       │      │                   ├── reutilizar id existente
                │       │      │                   ├── atualizar membresia_membros
                │       │      │                   └── criar novo rolpermanente
                │       │      │
                │       │      └── Própria igreja
                │       │             └── bloquear
                │       │
                │       └── Existente = ATIVO
                │              │
                │              ├── Própria igreja
                │              │      └── bloquear + orientar reintegração
                │              │
                │              └── Outra igreja
                │                     └── bloquear + orientar transferência
                │
                └── Inclusão = INATIVO
                        │
                        └── CPF existente
                               └── bloquear inclusão
```

---

# 5. Regras técnicas

1. A existência do CPF deve ser verificada **somente em `membresia_membros`**.

2. **Não consultar `membresia_migracao`** para essa validação.

3. A crítica deve ser realizada **no momento do salvamento**, juntamente com as demais validações existentes.

4. Quando for autorizada a inclusão de um membro **ATIVO** que já existe como **INATIVO em outra igreja**, **não criar novo registro em `membresia_membros`**.

5. Nesse caso, utilizar o `membresia_membros.id` existente.

6. A nova entrada como membro ativo deve gerar um **novo registro em `membresia_rolpermanente`**, preservando o histórico anterior.

7. O registro anterior de `membresia_rolpermanente` não deve ser sobrescrito ou excluído.

8. Quando o CPF já pertencer à própria igreja, a inclusão deve ser bloqueada.

9. Quando o CPF estiver ativo em outra igreja, a inclusão deve ser bloqueada e o usuário deve ser orientado a utilizar o processo de transferência.

10. Para inclusão como inativo, qualquer CPF já existente em `membresia_membros` deve bloquear a operação.

11. As mensagens devem utilizar os dados do registro encontrado para preencher:
    - Nome;
    - Igreja;
    - Distrito;
    - Região.

12. Igreja, distrito e região devem ser obtidos do registro existente em `membresia_membros`.

13. A atualização do membro existente e a criação do novo `membresia_rolpermanente` devem ocorrer em **uma única transação**.

14. Caso qualquer etapa da operação autorizada falhe, todas as alterações devem ser revertidas.

15. A lógica de atualização do membro existente deve reutilizar, quando aplicável, a mesma implementação da funcionalidade de **Reintegração de membros e congregados**, evitando duplicação de regras de negócio.

---

# 6. Comportamento esperado por cenário

| Inclusão | CPF existente | Igreja | Status existente | Resultado |
|---|---|---|---|---|
| ATIVO | Não | — | — | Prossegue fluxo atual |
| ATIVO | Sim | Outra | INATIVO | Pergunta confirmação; se SIM, reutiliza registro e cria novo `rolpermanente` |
| ATIVO | Sim | Própria | INATIVO | Bloqueia e orienta Reintegração |
| ATIVO | Sim | Própria | ATIVO | Bloqueia e orienta Reintegração |
| ATIVO | Sim | Outra | ATIVO | Bloqueia e orienta Transferência |
| INATIVO | Não | — | — | Prossegue fluxo atual |
| INATIVO | Sim | Qualquer | Qualquer | Bloqueia inclusão |

---

# 7. Critérios de aceite

## CPF não existente

- [ ] Informar CPF que não existe em `membresia_membros`.
- [ ] O fluxo atual de inclusão continua normalmente.
- [ ] Nenhuma consulta a `membresia_migracao` é utilizada para determinar a existência do CPF.

## Inclusão como ATIVO — membro inativo em outra igreja

- [ ] Informar CPF existente em `membresia_membros`.
- [ ] Identificar que o membro está INATIVO.
- [ ] Identificar que pertence a outra igreja.
- [ ] Exibir nome, igreja, distrito e região.
- [ ] Solicitar confirmação.
- [ ] Ao responder NÃO, cancelar sem alterar dados.
- [ ] Ao responder SIM, reutilizar o `membresia_membros.id` existente.
- [ ] Atualizar os dados conforme a nova inclusão.
- [ ] Criar novo registro em `membresia_rolpermanente`.
- [ ] Preservar o histórico anterior.
- [ ] Garantir transação da operação.

## Inclusão como ATIVO — pessoa já registrada na própria igreja

- [ ] Identificar CPF existente na igreja atual.
- [ ] Exibir mensagem orientando o uso da Reintegração.
- [ ] Bloquear o salvamento.
- [ ] Não criar novo `membresia_membros`.
- [ ] Não criar novo `membresia_rolpermanente`.

## Inclusão como ATIVO — membro ativo em outra igreja

- [ ] Identificar CPF existente como membro ATIVO.
- [ ] Identificar que pertence a outra igreja.
- [ ] Exibir igreja, distrito e região.
- [ ] Orientar o processo de transferência.
- [ ] Bloquear o salvamento.
- [ ] Não alterar o registro existente.

## Inclusão como INATIVO — CPF existente

- [ ] Identificar CPF existente em `membresia_membros`.
- [ ] Exibir mensagem informando que a pessoa já está registrada.
- [ ] Bloquear o salvamento.
- [ ] Não criar novo registro.
- [ ] Não alterar o registro existente.
