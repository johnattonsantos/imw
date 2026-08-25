SET @regiao_id = 23;
SET @data_inicial = '2025-11-01';
SET @data_final = CURDATE();

SELECT
  COALESCE(SUM(fl.valor), 0) /
  GREATEST(
    1,
    TIMESTAMPDIFF(
      MONTH,
      DATE_FORMAT(@data_inicial, '%Y-%m-01'),
      DATE_FORMAT(@data_final, '%Y-%m-01')
    ) + 1
  ) AS media_arrecadacao_mensal
FROM financeiro_lancamentos fl
WHERE fl.deleted_at IS NULL
  AND fl.tipo_lancamento = 'E'
  AND (fl.estornado = 0 OR fl.estornado IS NULL)
  AND fl.data_movimento BETWEEN @data_inicial AND @data_final
  AND (
    fl.hist_regiao_id = @regiao_id
    OR fl.instituicao_id = @regiao_id
    OR fl.instituicao_id IN (
      SELECT di.id
      FROM instituicoes_instituicoes di
      WHERE di.instituicao_pai_id = @regiao_id
        AND di.ativo = 1
        AND di.deleted_at IS NULL
    )
    OR fl.instituicao_id IN (
      SELECT ig.id
      FROM instituicoes_instituicoes ig
      JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
      WHERE di.instituicao_pai_id = @regiao_id
        AND ig.tipo_instituicao_id = 1
        AND ig.ativo = 1
        AND ig.deleted_at IS NULL
        AND di.ativo = 1
        AND di.deleted_at IS NULL
    )
  );