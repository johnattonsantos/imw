INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'evento', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'evento-novo', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-novo'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-novo'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-novo'
ORDER BY r.id DESC
LIMIT 1;



INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'evento-editar', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-editar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-editar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-editar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'evento-excluir', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-excluir'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-excluir'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-excluir'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'evento-funcao', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'evento-funcao-novo', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-novo'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-novo'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-novo'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'evento-funcao-editar', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-editar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-editar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-editar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'evento-funcao-excluir', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-excluir'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-excluir'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'evento-funcao-excluir'
ORDER BY r.id DESC
LIMIT 1;