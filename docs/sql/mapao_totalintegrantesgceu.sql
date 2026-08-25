SELECT COUNT(DISTINCT gm.membro_id) AS total_integrantes_gceus
FROM gceu_membros gm
JOIN gceu_cadastros gc ON gc.id = gm.gceu_cadastro_id
JOIN instituicoes_instituicoes ig ON ig.id = gc.instituicao_id
JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
WHERE di.instituicao_pai_id = 23
  AND gc.status = 'A'
  AND gc.deleted_at IS NULL
  AND gm.deleted_at IS NULL
  AND ig.tipo_instituicao_id = 1
  AND ig.ativo = 1
  AND ig.deleted_at IS NULL
  AND di.ativo = 1
  AND di.deleted_at IS NULL;