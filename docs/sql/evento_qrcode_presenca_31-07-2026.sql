-- SQL de producao - QR Code e presenca de inscritos em eventos
-- Data: 31/07/2026
-- Objetivo:
-- 1. Adicionar qr_token em evento_inscricoes.
-- 2. Criar evento_inscricao_movimentos para historico completo de entrada/saida.
-- 3. Registrar a migration como executada, caso este SQL seja aplicado manualmente.

SET @database_name := DATABASE();

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricoes'
      AND COLUMN_NAME = 'qr_token') = 0,
  'ALTER TABLE `evento_inscricoes` ADD COLUMN `qr_token` varchar(64) COLLATE utf8mb4_unicode_ci NULL AFTER `cpf`',
  'SELECT "Column evento_inscricoes.qr_token already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `evento_inscricoes`
   SET `qr_token` = UUID()
 WHERE `qr_token` IS NULL OR `qr_token` = '';

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricoes'
      AND INDEX_NAME = 'evento_inscricoes_qr_token_unique') = 0,
  'ALTER TABLE `evento_inscricoes` ADD UNIQUE KEY `evento_inscricoes_qr_token_unique` (`qr_token`)',
  'SELECT "Index evento_inscricoes_qr_token_unique already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `evento_inscricao_movimentos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `evento_inscricao_id` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registrado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `registrado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacoes` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evento_movimentos_evento_registrado_index` (`evento_id`, `registrado_em`),
  KEY `evento_movimentos_inscricao_registrado_index` (`evento_inscricao_id`, `registrado_em`),
  KEY `evento_inscricao_movimentos_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `evento_inscricao_movimentos_evento_id_foreign`
    FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evento_inscricao_movimentos_evento_inscricao_id_foreign`
    FOREIGN KEY (`evento_inscricao_id`) REFERENCES `evento_inscricoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evento_inscricao_movimentos_registrado_por_foreign`
    FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricao_movimentos'
      AND CONSTRAINT_NAME = 'evento_inscricao_movimentos_evento_inscricao_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `evento_inscricao_movimentos` ADD CONSTRAINT `evento_inscricao_movimentos_evento_inscricao_id_foreign` FOREIGN KEY (`evento_inscricao_id`) REFERENCES `evento_inscricoes` (`id`) ON DELETE CASCADE',
  'SELECT "Constraint evento_inscricao_movimentos_evento_inscricao_id_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricao_movimentos'
      AND CONSTRAINT_NAME = 'evento_inscricao_movimentos_evento_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `evento_inscricao_movimentos` ADD CONSTRAINT `evento_inscricao_movimentos_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE',
  'SELECT "Constraint evento_inscricao_movimentos_evento_id_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*)
     FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricao_movimentos'
      AND CONSTRAINT_NAME = 'evento_inscricao_movimentos_registrado_por_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `evento_inscricao_movimentos` ADD CONSTRAINT `evento_inscricao_movimentos_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL',
  'SELECT "Constraint evento_inscricao_movimentos_registrado_por_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT
  '2026_07_31_140000_add_qrcode_presenca_to_evento_inscricoes',
  COALESCE((SELECT MAX(`batch`) FROM `migrations`), 0) + 1
WHERE NOT EXISTS (
  SELECT 1
    FROM `migrations`
   WHERE `migration` = '2026_07_31_140000_add_qrcode_presenca_to_evento_inscricoes'
);
