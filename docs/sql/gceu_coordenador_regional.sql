INSERT INTO `perfils` (`id`, `nome`, `nivel`, `created_at`, `updated_at`, `deleted_at`) VALUES ('27', 'Coordenador Regional GCEU', 'I', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '27', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-menu-relatorio'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '27', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-regiao-relatorios'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '27', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-regiao-lista-aniversariantes'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '27', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-relatorio-diario'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '27', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-regiao-relatorio-carta-pastoral'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '27', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-regiao-lista-funcoes'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '27', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-regiao-lista-gceu'
ORDER BY r.id DESC
LIMIT 1;
