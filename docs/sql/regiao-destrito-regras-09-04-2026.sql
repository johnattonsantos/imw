INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-relatorio-aspirantes-igrejas', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-relatorio-aspirantes-igrejas'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '6', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-relatorio-aspirantes-igrejas'
ORDER BY r.id DESC
LIMIT 1;



INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'regiao-estatistica-gceu', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-estatistica-gceu'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '6', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'regiao-estatistica-gceu'
ORDER BY r.id DESC
LIMIT 1;




INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'distrito-estatistica-gceu', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '1', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-estatistica-gceu'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '2', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-estatistica-gceu'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '6', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'distrito-estatistica-gceu'
ORDER BY r.id DESC
LIMIT 1;