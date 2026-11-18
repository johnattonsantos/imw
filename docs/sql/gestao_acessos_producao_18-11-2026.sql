-- Gestao de Acessos: equivalente as duas migracoes de 18/11/2026.
-- MySQL 5.7+/8.0. Selecione o banco correto antes de executar.
-- Fazer backup, publicar em manutencao e interromper a execucao se houver erro.
-- DDL no MySQL faz COMMIT implicito; o script inteiro nao e reversivel por ROLLBACK.
-- Nao apaga registros nem tenta reconstruir sessoes antigas da auditoria.
-- Pode ser reaplicado sem duplicar permissoes ou reiniciar o monitoramento.

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET SESSION time_zone = '-03:00';
SET @gestao_agora = CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS gestao_acessos_configuracao (
    id INT UNSIGNED NOT NULL,
    inicio_monitoramento DATETIME NOT NULL,
    inicio_sessoes DATETIME NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compatibilidade com bancos que ja receberam somente a primeira migracao.
SET @gestao_coluna_sql = IF(
    EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'gestao_acessos_configuracao'
          AND column_name = 'inicio_sessoes'
    ),
    'SELECT 1',
    'ALTER TABLE gestao_acessos_configuracao ADD COLUMN inicio_sessoes DATETIME NULL'
);
PREPARE gestao_coluna_stmt FROM @gestao_coluna_sql;
EXECUTE gestao_coluna_stmt;
DEALLOCATE PREPARE gestao_coluna_stmt;

CREATE TABLE IF NOT EXISTS gestao_acessos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    regiao_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    perfil_id BIGINT UNSIGNED NOT NULL,
    instituicao_id BIGINT UNSIGNED NOT NULL,
    data_acesso DATE NOT NULL,
    primeiro_acesso DATETIME NOT NULL,
    ultimo_acesso DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY gestao_acessos_presenca_unica (user_id, perfil_id, instituicao_id, data_acesso),
    KEY gestao_acessos_regiao_id_data_acesso_index (regiao_id, data_acesso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gestao_acessos_sessoes (
    id CHAR(36) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    login_em DATETIME NULL,
    observada_em DATETIME NOT NULL,
    logout_em DATETIME NULL,
    PRIMARY KEY (id),
    KEY gestao_acessos_sessoes_user_id_index (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gestao_acessos_sessao_perfis (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    sessao_id CHAR(36) NOT NULL,
    regiao_id BIGINT UNSIGNED NOT NULL,
    perfil_id BIGINT UNSIGNED NOT NULL,
    instituicao_id BIGINT UNSIGNED NOT NULL,
    usuario_nome VARCHAR(191) NOT NULL,
    perfil_nome VARCHAR(191) NOT NULL,
    instituicao_nome VARCHAR(191) NOT NULL,
    primeiro_uso DATETIME NOT NULL,
    ultimo_uso DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY gestao_sessao_perfil_unico (sessao_id, perfil_id, instituicao_id),
    KEY gestao_acessos_sessao_perfis_regiao_id_ultimo_uso_index (regiao_id, ultimo_uso),
    CONSTRAINT gestao_acessos_sessao_perfis_sessao_id_foreign
        FOREIGN KEY (sessao_id) REFERENCES gestao_acessos_sessoes (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

INSERT INTO gestao_acessos_configuracao (id, inicio_monitoramento, inicio_sessoes)
SELECT 1, @gestao_agora, @gestao_agora
WHERE NOT EXISTS (SELECT 1 FROM gestao_acessos_configuracao WHERE id = 1);

UPDATE gestao_acessos_configuracao
SET inicio_sessoes = @gestao_agora
WHERE id = 1 AND inicio_sessoes IS NULL;

SET @gestao_regra_id = (
    SELECT id FROM regras WHERE nome = 'regiao-gestao-acessos' ORDER BY id LIMIT 1
);

INSERT INTO regras (nome, created_at, updated_at, deleted_at)
SELECT 'regiao-gestao-acessos', @gestao_agora, @gestao_agora, NULL
WHERE @gestao_regra_id IS NULL;

SET @gestao_regra_id = (
    SELECT id FROM regras WHERE nome = 'regiao-gestao-acessos' ORDER BY id LIMIT 1
);

UPDATE regras SET deleted_at = NULL WHERE id = @gestao_regra_id;

-- Apenas o Administrador Regiao ativo recebe acesso ao relatorio.
INSERT INTO perfil_regra (perfil_id, regra_id, created_at, updated_at)
SELECT p.id, @gestao_regra_id, @gestao_agora, @gestao_agora
FROM perfils AS p
WHERE p.nivel = 'R'
  AND p.deleted_at IS NULL
  AND p.nome IN ('Administrador Região', 'Administrador - Região', 'Administrador Regiao', 'Administrador - Regiao')
  AND NOT EXISTS (
      SELECT 1 FROM perfil_regra AS pr
      WHERE pr.perfil_id = p.id AND pr.regra_id = @gestao_regra_id
  );

-- Sincronizar o controle do Laravel somente depois das etapas anteriores.
-- O banco deve ser de uma instalacao Laravel existente, com a tabela migrations.
SET @gestao_batch = (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations);

INSERT INTO migrations (migration, batch)
SELECT '2026_11_18_120000_create_gestao_acessos', @gestao_batch
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE migration = '2026_11_18_120000_create_gestao_acessos'
);

INSERT INTO migrations (migration, batch)
SELECT '2026_11_18_130000_create_gestao_acessos_sessoes', @gestao_batch
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE migration = '2026_11_18_130000_create_gestao_acessos_sessoes'
);

COMMIT;

-- Conferencia final.
SELECT id, inicio_monitoramento, inicio_sessoes FROM gestao_acessos_configuracao;

SELECT p.id AS perfil_id, p.nome AS perfil, r.nome AS permissao
FROM perfils AS p
INNER JOIN perfil_regra AS pr ON pr.perfil_id = p.id
INNER JOIN regras AS r ON r.id = pr.regra_id
WHERE r.id = @gestao_regra_id AND p.deleted_at IS NULL;

SELECT migration, batch FROM migrations
WHERE migration IN (
    '2026_11_18_120000_create_gestao_acessos',
    '2026_11_18_130000_create_gestao_acessos_sessoes'
);

-- Apos publicar o codigo e executar este SQL:
-- php artisan route:clear
-- php artisan view:clear
