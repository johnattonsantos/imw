INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-ebd-alunos', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-ebd-liderancas', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-ebd-professores', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-ebd-geral', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-ebd-classes', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-ebd-turmas', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-ebd-diarios', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-ebd-agendas', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-buscar-membro', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-cadastrar-visitante', current_timestamp(), current_timestamp(), NULL);



INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-ebd-alunos'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-ebd-liderancas'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-ebd-professores'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-ebd-geral'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-ebd-classes'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-ebd-turmas'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-ebd-diarios'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-ebd-agendas'
ORDER BY r.id DESC
LIMIT 1;

