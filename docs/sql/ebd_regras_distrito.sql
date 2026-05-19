INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-ebd-alunos', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-ebd-liderancas', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-ebd-professores', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-ebd-geral', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-ebd-classes', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-ebd-turmas', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-ebd-diarios', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-ebd-agendas', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 
'distrito-ebd-estatisticas', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-buscar-membro', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-cadastrar-visitante', current_timestamp(), current_timestamp(), NULL);



INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-alunos'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-liderancas'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-professores'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-geral'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-classes'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-turmas'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-diarios'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-agendas'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-ebd-estatisticas'
ORDER BY r.id DESC
LIMIT 1;

