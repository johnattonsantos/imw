SELECT COUNT(DISTINCT mfm.membro_id) AS total_integrantes_ministerios
FROM membresia_funcoesministeriais mfm
JOIN membresia_membros mm ON mm.id = mfm.membro_id
WHERE mm.regiao_id = 23
  AND mm.status = 'A'
  AND mm.vinculo = 'M'
  AND mm.deleted_at IS NULL
  AND mfm.deleted_at IS NULL
  AND mfm.data_saida IS NULL;