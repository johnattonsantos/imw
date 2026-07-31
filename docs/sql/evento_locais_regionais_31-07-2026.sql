-- SQL de producao - Locais de eventos por regiao
-- Data: 31/07/2026
-- Objetivo:
-- 1. Criar a tabela evento_locais para cadastrar locais exclusivos da regiao logada.
-- 2. Adicionar evento_local_id em eventos para vincular eventos regionais ao local criado.
-- 3. Registrar a migration como executada, caso este SQL seja aplicado manualmente.

SET @database_name := DATABASE();

CREATE TABLE IF NOT EXISTS `evento_locais` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `regiao_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `endereco` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evento_locais_regiao_ativo_index` (`regiao_id`, `ativo`),
  CONSTRAINT `evento_locais_regiao_id_foreign`
    FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_locais'
      AND INDEX_NAME = 'evento_locais_regiao_ativo_index') = 0,
  'ALTER TABLE `evento_locais` ADD INDEX `evento_locais_regiao_ativo_index` (`regiao_id`, `ativo`)',
  'SELECT "Index evento_locais_regiao_ativo_index already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_locais'
      AND CONSTRAINT_NAME = 'evento_locais_regiao_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `evento_locais` ADD CONSTRAINT `evento_locais_regiao_id_foreign` FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE',
  'SELECT "Constraint evento_locais_regiao_id_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'eventos'
      AND COLUMN_NAME = 'evento_local_id') = 0,
  'ALTER TABLE `eventos` ADD COLUMN `evento_local_id` bigint(20) UNSIGNED NULL AFTER `instituicao_id`',
  'SELECT "Column eventos.evento_local_id already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'eventos'
      AND CONSTRAINT_NAME = 'eventos_evento_local_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `eventos` ADD CONSTRAINT `eventos_evento_local_id_foreign` FOREIGN KEY (`evento_local_id`) REFERENCES `evento_locais` (`id`) ON DELETE SET NULL',
  'SELECT "Constraint eventos_evento_local_id_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


