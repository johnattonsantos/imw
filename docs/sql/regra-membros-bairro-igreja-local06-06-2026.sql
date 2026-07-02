INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'relatorio-membros-por-bairro', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'relatorio-membros-por-bairro'
ORDER BY r.id DESC
LIMIT 1;
