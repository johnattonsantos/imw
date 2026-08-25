SELECT COUNT(*) AS total_clerigos
FROM pessoas_pessoas
WHERE regiao_id = 23
  AND situacao_id = 1
  AND LOWER(categoria) IN ('ministro', 'pastor', 'missionária', 'missionaria')
  AND deleted_at IS NULL;