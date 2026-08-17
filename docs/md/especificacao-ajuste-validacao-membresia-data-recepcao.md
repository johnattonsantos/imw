# Especificação — Ajuste na Validação de Membresia para CPF Existente como Membro Inativo

## 1. Objetivo

Ajustar a funcionalidade **Membresia → Validação** para incluir uma validação adicional no fluxo em que:

- o usuário está incluindo um membro como **ATIVO**;
- o CPF já existe em `membresia_membros`;
- a pessoa está registrada como **membro INATIVO em outra igreja**;
- o usuário confirma que deseja prosseguir com a inclusão.

A nova validação deve garantir que a **data de recepção na nova igreja seja posterior à data de exclusão na igreja anterior**.

> Este documento é um **ajuste complementar** à especificação anteriormente implementada. Não alterar os demais comportamentos já definidos, exceto no ponto descrito neste documento.

---

# 2. Fluxo que deve receber o ajuste

Quando o CPF já existir em `membresia_membros` como membro **INATIVO em outra igreja**, o sistema deve apresentar a confirmação já existente:

> **Este CPF pertence a [NOME], que está registrado como membro INATIVO na igreja [IGREJA], distrito [DISTRITO], [X]ª região. Deseja continuar com a inclusão?**

O novo comportamento deve ser executado **depois que o usuário responder SIM**.

---

# 3. Validação da data de recepção

Após o usuário confirmar que deseja prosseguir, validar a **data de recepção informada para a nova igreja** em relação à **data de exclusão registrada na igreja anterior**.

A regra é:

```text
data_recepcao > data_exclusao_igreja_anterior
```

A data de recepção deve ser **estritamente posterior** à data de exclusão.

Portanto:

- se a data de recepção for igual à data de exclusão, não permitir;
- se a data de recepção for anterior à data de exclusão, não permitir;
- somente se for posterior, continuar o fluxo de inclusão.

### Exemplos

| Data de exclusão | Data de recepção | Resultado |
|---|---|---|
| 10/08/2026 | 11/08/2026 | Permitido |
| 10/08/2026 | 10/08/2026 | Não permitido |
| 10/08/2026 | 09/08/2026 | Não permitido |

---

# 4. Mensagem de erro

Quando a data de recepção for **igual ou anterior** à data de exclusão da igreja anterior, exibir:

> **Data de recepção não permitida, pois o membro estava ativo na igreja [IGREJA] até o dia [DATA]. Volte à tela inicial, corrija a data de recepção e continue com a validação.**

Onde:

- `[IGREJA]` = nome da igreja anterior;
- `[DATA]` = data de exclusão do membro na igreja anterior.

A mensagem deve ser curta e clara, sem apresentar detalhes técnicos da regra.

---

# 5. Comportamento da tela de erro

A mensagem deve ser apresentada em uma tela/modal que contenha **somente um botão**:

> **Fechar**

Não apresentar outros botões ou ações de confirmação.

Ao clicar em **Fechar**:

1. retornar à **tela de validação de membresia**;
2. manter todos os campos preenchidos anteriormente;
3. permitir que o usuário corrija a data de recepção;
4. exigir que o usuário clique novamente em **Validar** para executar novamente o processo.

---

# 6. Preservação dos dados preenchidos

Ao retornar para a tela de validação, **nenhum campo deve ser perdido**.

Devem permanecer preenchidos, conforme o estado anterior à validação:

- CPF;
- dados da pessoa;
- status;
- data de recepção;
- demais campos preenchidos pelo usuário.

O usuário não deve precisar:

- informar novamente o CPF;
- preencher novamente os dados;
- reiniciar a operação.

A intenção é permitir que o usuário apenas corrija a **data de recepção** e clique novamente em **Validar**.

---

# 7. Fluxo completo

```text
Usuário informa CPF
        │
        └── Clica em Validar
                │
                └── CPF existe em membresia_membros
                        │
                        └── Inclusão como ATIVO
                                │
                                └── Membro INATIVO em outra igreja
                                        │
                                        └── Exibir confirmação
                                              │
                                              ├── NÃO
                                              │    └── Cancela operação
                                              │
                                              └── SIM
                                                   │
                                                   └── Validar
                                                       data de recepção
                                                        │
                                                        ├── data_recepcao
                                                        │   > data_exclusao
                                                        │      │
                                                        │      └── Prossegue
                                                        │          com o fluxo
                                                        │          existente
                                                        │
                                                        └── data_recepcao
                                                            <= data_exclusao
                                                                 │
                                                                 └── Exibir mensagem
                                                                     │
                                                                     └── Fechar
                                                                          │
                                                                          └── Retornar
                                                                              à tela
                                                                              de validação
                                                                              mantendo
                                                                              os campos
                                                                              preenchidos
```

---

# 8. Regra de implementação

A data de exclusão deve ser obtida a partir do registro existente em `membresia_membros` encontrado para o CPF.

Não criar ou atualizar o registro de `membresia_membros` antes de concluir essa validação.

A sequência deve ser:

1. localizar o CPF em `membresia_membros`;
2. identificar que o membro está INATIVO;
3. identificar que pertence a outra igreja;
4. solicitar confirmação ao usuário;
5. somente se o usuário confirmar, validar a data de recepção;
6. se a data for inválida, interromper o processo;
7. exibir a mensagem;
8. ao clicar em **Fechar**, retornar à tela preservando todos os dados preenchidos;
9. se a data for válida, continuar o fluxo de inclusão já existente.

---

# 9. Critérios de aceite

- [ ] No fluxo de inclusão como ATIVO, quando o CPF existir como membro INATIVO em outra igreja, o sistema continua apresentando a confirmação existente.
- [ ] A nova validação ocorre somente após o usuário confirmar que deseja prosseguir.
- [ ] A data de recepção deve ser estritamente posterior à data de exclusão da igreja anterior.
- [ ] Data de recepção igual à data de exclusão deve ser rejeitada.
- [ ] Data de recepção anterior à data de exclusão deve ser rejeitada.
- [ ] A mensagem exibida deve informar a igreja e a data de exclusão.
- [ ] A mensagem deve orientar o usuário a corrigir a data e executar novamente a validação.
- [ ] A tela de mensagem deve possuir somente o botão **Fechar**.
- [ ] Ao fechar a mensagem, o usuário retorna à tela de validação.
- [ ] CPF e todos os demais campos anteriormente preenchidos devem permanecer preenchidos.
- [ ] O usuário deve conseguir corrigir a data e clicar novamente em **Validar**.
- [ ] O processo de inclusão não deve ser executado enquanto a data de recepção for inválida.
- [ ] Com data válida, o fluxo existente de inclusão deve continuar normalmente.
- [ ] Os demais cenários de tratamento de CPF já implementados não devem ser alterados.
