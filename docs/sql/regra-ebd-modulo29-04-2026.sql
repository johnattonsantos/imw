-- Regras do módulo EBD
-- Data: 29/04/2026

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-dashboard', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-liderancas', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-professores', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-alunos', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-classes', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-turmas', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-diarios', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-agendas', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-buscar-membro', current_timestamp(), current_timestamp(), NULL);
INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'ebd-cadastrar-visitante', current_timestamp(), current_timestamp(), NULL);

-- Vinculação de regras aos perfis padrão (Administrador=1, Secretário=4, Pastor=7)

-- ebd-dashboard
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-dashboard' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-dashboard' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-dashboard' ORDER BY r.id DESC LIMIT 1;

-- ebd-liderancas
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-liderancas' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-liderancas' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-liderancas' ORDER BY r.id DESC LIMIT 1;

-- ebd-professores
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-professores' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-professores' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-professores' ORDER BY r.id DESC LIMIT 1;

-- ebd-alunos
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-alunos' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-alunos' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-alunos' ORDER BY r.id DESC LIMIT 1;

-- ebd-classes
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-classes' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-classes' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-classes' ORDER BY r.id DESC LIMIT 1;

-- ebd-turmas
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-turmas' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-turmas' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-turmas' ORDER BY r.id DESC LIMIT 1;

-- ebd-diarios
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-diarios' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-diarios' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-diarios' ORDER BY r.id DESC LIMIT 1;

-- ebd-agendas
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-agendas' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-agendas' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-agendas' ORDER BY r.id DESC LIMIT 1;

-- ebd-buscar-membro
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-buscar-membro' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-buscar-membro' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-buscar-membro' ORDER BY r.id DESC LIMIT 1;

-- ebd-cadastrar-visitante
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-cadastrar-visitante' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-cadastrar-visitante' ORDER BY r.id DESC LIMIT 1;
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp() FROM regras r WHERE r.nome = 'ebd-cadastrar-visitante' ORDER BY r.id DESC LIMIT 1;
