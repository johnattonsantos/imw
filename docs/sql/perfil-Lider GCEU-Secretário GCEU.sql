INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-atualizar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-atualizar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-cadastrar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-cadastrar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-excluir'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-excluir'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-igreja-lista-aniversariantes'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-igreja-lista-aniversariantes'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-diario'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-diario'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-igreja-lista-funcoes'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-igreja-lista-funcoes'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-igreja-lista-gceu'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-igreja-lista-gceu'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-igreja-relatorios'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-igreja-relatorios'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-lista'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-lista'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-membros'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-membros'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-relatorio-diario'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu-relatorio-diario'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '12', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'gceu'
ORDER BY r.id DESC
LIMIT 1;
