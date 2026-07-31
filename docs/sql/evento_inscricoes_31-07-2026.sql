-- SQL de producao - Inscritos no evento
-- Data: 31/07/2026
-- Objetivo:
-- 1. Criar a tabela evento_inscricoes para vincular membros/clerigos aos eventos por CPF.
-- 2. Manter os dados principais do inscrito no momento da inscricao para relatorio.
-- 3. Registrar a migration como executada, caso este SQL seja aplicado manualmente.

SET @database_name := DATABASE();

CREATE TABLE IF NOT EXISTS `evento_inscricoes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `origem` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `membro_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pessoa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cpf` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funcao_eclesiastica` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `igreja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `igreja_nome` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `evento_inscricoes_evento_cpf_unique` (`evento_id`, `cpf`),
  KEY `evento_inscricoes_evento_origem_index` (`evento_id`, `origem`),
  KEY `evento_inscricoes_membro_id_foreign` (`membro_id`),
  KEY `evento_inscricoes_pessoa_id_foreign` (`pessoa_id`),
  KEY `evento_inscricoes_igreja_id_foreign` (`igreja_id`),
  CONSTRAINT `evento_inscricoes_evento_id_foreign`
    FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evento_inscricoes_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `evento_inscricoes_membro_id_foreign`
    FOREIGN KEY (`membro_id`) REFERENCES `membresia_membros` (`id`) ON DELETE SET NULL,
  CONSTRAINT `evento_inscricoes_pessoa_id_foreign`
    FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas_pessoas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricoes'
      AND INDEX_NAME = 'evento_inscricoes_evento_cpf_unique') = 0,
  'ALTER TABLE `evento_inscricoes` ADD UNIQUE KEY `evento_inscricoes_evento_cpf_unique` (`evento_id`, `cpf`)',
  'SELECT "Index evento_inscricoes_evento_cpf_unique already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricoes'
      AND INDEX_NAME = 'evento_inscricoes_evento_origem_index') = 0,
  'ALTER TABLE `evento_inscricoes` ADD INDEX `evento_inscricoes_evento_origem_index` (`evento_id`, `origem`)',
  'SELECT "Index evento_inscricoes_evento_origem_index already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricoes'
      AND CONSTRAINT_NAME = 'evento_inscricoes_evento_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `evento_inscricoes` ADD CONSTRAINT `evento_inscricoes_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE',
  'SELECT "Constraint evento_inscricoes_evento_id_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricoes'
      AND CONSTRAINT_NAME = 'evento_inscricoes_membro_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `evento_inscricoes` ADD CONSTRAINT `evento_inscricoes_membro_id_foreign` FOREIGN KEY (`membro_id`) REFERENCES `membresia_membros` (`id`) ON DELETE SET NULL',
  'SELECT "Constraint evento_inscricoes_membro_id_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricoes'
      AND CONSTRAINT_NAME = 'evento_inscricoes_pessoa_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `evento_inscricoes` ADD CONSTRAINT `evento_inscricoes_pessoa_id_foreign` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas_pessoas` (`id`) ON DELETE SET NULL',
  'SELECT "Constraint evento_inscricoes_pessoa_id_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'evento_inscricoes'
      AND CONSTRAINT_NAME = 'evento_inscricoes_igreja_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `evento_inscricoes` ADD CONSTRAINT `evento_inscricoes_igreja_id_foreign` FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE SET NULL',
  'SELECT "Constraint evento_inscricoes_igreja_id_foreign already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT
  '2026_07_31_130000_create_evento_inscricoes_table',
  COALESCE((SELECT MAX(`batch`) FROM `migrations`), 0) + 1
WHERE NOT EXISTS (
  SELECT 1
    FROM `migrations`
   WHERE `migration` = '2026_07_31_130000_create_evento_inscricoes_table'
);
