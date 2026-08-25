-- Média de Exclusões por Trimestre
SET @regiao_id = 23;
SET @data_inicial = '2025-11-01';
SET @data_final = CURDATE();

SELECT
  COUNT(*) /
  GREATEST(
    1,
    ((YEAR(@data_final) * 4) + CEIL(MONTH(@data_final) / 3)) -
    ((YEAR(@data_inicial) * 4) + CEIL(MONTH(@data_inicial) / 3)) + 1
  ) AS media_exclusoes_trimestre
FROM membresia_rolpermanente mr
WHERE mr.regiao_id = @regiao_id
  AND mr.status = 'I'
  AND mr.deleted_at IS NULL
  AND mr.dt_exclusao BETWEEN @data_inicial AND @data_final;