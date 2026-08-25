SELECT COUNT(*) AS total_congregados
FROM membresia_membros
WHERE regiao_id = 23
  AND status = 'A'
  AND vinculo = 'C'
  AND deleted_at IS NULL;