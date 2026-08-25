SELECT COUNT(*) AS total_membros
FROM membresia_membros
WHERE regiao_id = 23
  AND status = 'A'
  AND vinculo = 'M'
  AND deleted_at IS NULL;