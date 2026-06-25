INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'patrimonio-dashboard', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'patrimonio-dashboard'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'patrimonio-bens-imoveis', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'patrimonio-bens-imoveis'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'patrimonio-bens-moveis', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'patrimonio-bens-moveis'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'patrimonio-documentos', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'patrimonio-documentos'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'patrimonio-benfeitoria', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'patrimonio-benfeitoria'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'patrimonio-baixa', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'patrimonio-baixa'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'patrimonio-relatorios', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'patrimonio-relatorios'
ORDER BY r.id DESC
LIMIT 1;

