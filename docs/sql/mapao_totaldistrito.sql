SELECT COUNT(*) AS total_distritos
FROM instituicoes_instituicoes
WHERE instituicao_pai_id = 23
  AND ativo = 1
  AND deleted_at IS NULL;