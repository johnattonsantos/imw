SELECT COUNT(*) AS total_igrejas
FROM instituicoes_instituicoes ig
JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
WHERE di.instituicao_pai_id = 23
  AND ig.tipo_instituicao_id = 1
  AND ig.ativo = 1
  AND ig.deleted_at IS NULL
  AND di.ativo = 1
  AND di.deleted_at IS NULL;