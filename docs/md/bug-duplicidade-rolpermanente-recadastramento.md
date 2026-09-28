# Bug: duplicidade de registros em `membresia_rolpermanente` no fluxo de recadastramento

## Sintoma observado

Divergência entre dois números que deveriam bater:

- **Relatório "Quantidade de Membros"** (`/regiao/relatorio/quantidademembros`)
- **Gráfico 4 "Distribuição Regional por Vínculo Ativo"** (dashboard da home do módulo regional, barra "Membros")

Na região 23, com `data_final = 2026-09-27`: o relatório mostrava **22.007** e o gráfico **22.004** — diferença de **3 membros**.

## Investigação

Foram reconstruídas as duas queries de origem:

- Relatório: `app/Traits/QuantidadeMembrosUtils.php` (método `fetch`), usado por `app/Services/ServiceRegiaoRelatorios/QuantidadeMembrosService.php`.
- Gráfico: `app/Http/Controllers/HomeController.php` (~linha 420-444, variável `$regiaoVinculosTotais`).

Comparando membro a membro (contando membros **distintos** dos dois lados, com os mesmos critérios de vínculo/status/data), os dois cálculos batem exatamente em **22.004**. A diferença de **3** só aparece porque o relatório soma `COUNT(CASE WHEN ... THEN mm.id ELSE NULL END)` — que conta **linhas** retornadas pelo JOIN com `membresia_rolpermanente`, não membros únicos. Quando um membro tem mais de uma linha em `membresia_rolpermanente` "ativa" na mesma janela de data, ele é somado mais de uma vez no total do relatório.

**Decisão**: o relatório não será alterado. Foi justamente essa diferença de contagem que expôs a existência de linhas duplicadas em `membresia_rolpermanente` — o gráfico do dashboard, que usa `lastrec = 1` (só a última linha por membro), mascara o problema. O relatório funcionou, sem querer, como uma checagem de integridade dos dados.

## Causa raiz

Um dos três membros duplicados (`membro_id = 21d61d7d-e78b-422b-b689-012b1c4270a5`) tem este histórico em `membresia_rolpermanente`:

| id | status | numero_rol | dt_recepcao | dt_exclusao | igreja_id | lastrec | updated_at |
|---|---|---|---|---|---|---|---|
| 8.414 | I | 355 | 2012-11-11 | 2026-06-07 | 1.911 | 0 | 2026-09-24 03:29:09 |
| 91.823 | A | 2.225 | 2026-09-24 | NULL | 2.226 | 0 | 2026-09-24 03:29:09 |
| 91.824 | A | 2.225 | 2026-09-24 | NULL | 2.226 | 1 | 2026-09-24 03:29:09 |

As linhas 91.823 e 91.824 são **idênticas** em todos os campos de negócio (mesmo `numero_rol`, `dt_recepcao`, `igreja_id`, `distrito_id`, `regiao_id`, `modo_recepcao_id`), diferindo só no `id` e no `lastrec`. Todas as três linhas foram tocadas **no mesmo segundo**.

Isso é a assinatura de `app/Services/ServiceMembrosGeral/UpdateMembroRecadastramentoService.php`, método `updateDadosRolPermanente()` (linhas 447-489), executado com `$criarNovoRegistro = true` (caminho usado quando o recadastramento reaproveita um membro inativo de outra igreja — `reutilizandoMembroInativoOutraIgreja`):

```php
if ($criarNovoRegistro) {
    MembresiaRolPermanente::where('membro_id', $membroId)
        ->where('lastrec', 1)
        ->update(['lastrec' => 0]);

    MembresiaRolPermanente::create(array_merge($payload, [
        'membro_id' => $membroId,
    ]));

    return;
}
```

Reconstruindo a sequência:

1. **1ª execução**: zera `lastrec` da linha 8.414 (que ainda estava `lastrec = 1`, mesmo já excluída desde 2026-06-07) → cria a linha **91.823** com `lastrec = 1`.
2. **2ª execução (repetida)**: encontra a linha 91.823 como a atual (`lastrec = 1`) e zera ela também → cria a linha **91.824**, que fica como a definitiva (`lastrec = 1`).

Ou seja, **o método `execute()` do service rodou duas vezes para o mesmo recadastramento**, no mesmo segundo. A linha órfã 91.823 (`lastrec = 0`, mas `dt_exclusao = NULL` e `dt_recepcao` dentro da janela do relatório) passa a ser contada pelo relatório junto com a 91.824, gerando a duplicidade.

### Por que a execução rodou duas vezes

O controller já envolve a chamada em transação:

```php
// app/Http/Controllers/MembrosController.php:372-383
public function updateRecadastramento(UpdateMembroRequest $request, $id)
{
    ...
    DB::beginTransaction();
    app(UpdateMembroRecadastramentoService::class)->execute($request->all(), MembresiaMembro::VINCULO_MEMBRO);
    DB::commit();
    ...
}
```

Isso garante atomicidade **dentro de uma requisição**, mas não impede que **duas requisições HTTP separadas** cheguem na mesma rota para o mesmo recadastramento (reenvio de formulário por duplo clique, timeout do navegador com nova tentativa, etc.). Não há nenhuma trava de idempotência: o service não verifica se aquele recadastramento já foi processado antes de rodar todo o fluxo de novo. O flag `validado` (em `membresia_membro_recadastramentos` e `membresia_membros`) só é setado no **fim** de `execute()` (método `updateValidadoFlags`, linha 491-495) — tarde demais para impedir uma segunda requisição que já estava em andamento.

## Impacto

- Infla contagens em qualquer relatório/tela que não filtre por `lastrec = 1` (como o relatório "Quantidade de Membros").
- Gera lixo de dados em `membresia_rolpermanente` (linhas "fantasma" com `lastrec = 0` mas sem `dt_exclusao`, que nunca deveriam existir).
- Provavelmente não é exclusivo do fluxo de recadastramento — qualquer outro service que siga o mesmo padrão (zera `lastrec` do atual + cria um novo registro, sem trava) está sujeito ao mesmo problema em caso de reenvio de formulário.

## Recomendação de correção

### 1. Impedir reprocessamento duplicado (prioridade alta)

Em `UpdateMembroRecadastramentoService::execute()`, no início do método, buscar o registro de `MembresiaMembroRecadastramento` com **lock pessimista** (`lockForUpdate()`) dentro da transação já aberta pelo controller, e checar o flag `validado` antes de prosseguir:

```php
$membroMigracao = MembresiaMembroRecadastramento::where('id', $membroMigracaoId)
    ->lockForUpdate()
    ->first();

if (!$membroMigracao) {
    // tratamento já existente para "não encontrado"
}

if ($membroMigracao->validado) {
    throw new RecadastramentoJaValidadoException();
}
```

Com o `lockForUpdate()`, se duas requisições chegarem quase simultaneamente, a segunda vai esperar a transação da primeira commitar (que já terá marcado `validado = 1`) e então cair no `throw`, em vez de reprocessar tudo. O controller trataria essa exceção com uma mensagem amigável ("este recadastramento já foi validado"), no mesmo padrão usado hoje para `CpfDuplicadoConfirmacaoNecessariaException` (`MembrosController::updateRecadastramento`, linhas 372-401).

Vale avaliar se o mesmo padrão de trava deve ser replicado em outros services que seguem a lógica "zera lastrec atual + cria novo registro" (ex.: `StoreTransferenciaInternaService`, `StoreReceberMembroExternoService`, `StoreReintegracaoService`), já que todos são candidatos ao mesmo tipo de duplicidade em caso de duplo submit.

### 2. Frontend — proteção complementar

Desabilitar o botão de submit assim que clicado (ou usar um token de idempotência por submissão) nas telas de recadastramento, transferência e recepção de membro, para reduzir a chance de reenvio duplicado pelo navegador.

## Resumo para o dev

- **Não mexer** no relatório "Quantidade de Membros" — ele está correto na regra de negócio e foi essencial para achar o bug.
- **Corrigir**: `UpdateMembroRecadastramentoService::execute()` precisa de uma trava de idempotência (lock + checagem de `validado`) para não reprocessar um recadastramento já validado.
- **Avaliar**: replicar a mesma trava em services semelhantes (transferência interna, recepção de membro externo, reintegração).
