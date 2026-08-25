SELECT COUNT(*) AS total_congregacoes
FROM congregacoes_congregacoes cc
JOIN instituicoes_instituicoes ig ON ig.id = cc.instituicao_id
JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
WHERE di.instituicao_pai_id = 23
  AND ig.tipo_instituicao_id = 1
  AND ig.ativo = 1
  AND ig.deleted_at IS NULL
  AND di.ativo = 1
  AND di.deleted_at IS NULL
  AND cc.ativo = 1
  AND cc.deleted_at IS NULL;