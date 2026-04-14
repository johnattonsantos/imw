-- SQL consolidado das migrations de perfis CRIE/Admin (2026-03-30 a 2026-04-14)
-- Projeto: IMW
-- Observacao: script pensado para MySQL/MariaDB e com protecoes para reexecucao.

START TRANSACTION;

-- ---------------------------------------------------------------------------
-- [2026_03_30_220000_add_regiao_id_to_users_table]
-- Add users.regiao_id + FK para instituicoes_instituicoes(id)
-- ---------------------------------------------------------------------------
SET @col_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'regiao_id'
);
SET @sql := IF(
    @col_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `regiao_id` BIGINT UNSIGNED NULL AFTER `pessoa_id`',
    'SELECT "users.regiao_id ja existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
    SELECT COUNT(*)
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'regiao_id'
      AND REFERENCED_TABLE_NAME = 'instituicoes_instituicoes'
);
SET @sql := IF(
    @fk_exists = 0,
    'ALTER TABLE `users` ADD CONSTRAINT `users_regiao_id_foreign` FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes`(`id`) ON DELETE SET NULL',
    'SELECT "FK users.regiao_id ja existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------------
-- [2026_03_30_230000_normalize_core_profile_names]
-- Normaliza nomes dos perfis core
-- ---------------------------------------------------------------------------
UPDATE `perfils`
SET `nome` = 'administrador_sistema'
WHERE `nome` IN ('Administrador do Sistema', 'administrador do sistema');

UPDATE `perfils`
SET `nome` = 'crie'
WHERE `nome` IN ('CRIE', 'Crie');

-- ---------------------------------------------------------------------------
-- [2026_04_07_090000_create_or_restore_crie_profile]
-- Cria/restaura perfil CRIE e vincula regras basicas
-- ---------------------------------------------------------------------------
INSERT INTO `perfils` (`nome`, `nivel`, `created_at`, `updated_at`)
SELECT 'crie', 'R', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `perfils` WHERE `nome` = 'crie'
);

UPDATE `perfils`
SET `nivel` = 'R', `updated_at` = NOW()
WHERE `nome` = 'crie';

INSERT INTO `perfil_regra` (`perfil_id`, `regra_id`, `created_at`, `updated_at`)
SELECT p.id, r.id, NOW(), NOW()
FROM `perfils` p
JOIN `regras` r ON r.`nome` IN (
    'admin-index',
    'menu-usuarios-instituicao',
    'usuarios-index',
    'usuarios-cadastrar',
    'usuarios-atualizar',
    'usuarios-editar',
    'usuarios-excluir',
    'usuarios-pesquisar'
)
LEFT JOIN `perfil_regra` pr
    ON pr.`perfil_id` = p.`id`
   AND pr.`regra_id` = r.`id`
WHERE p.`nome` = 'crie'
  AND pr.`id` IS NULL;

-- ---------------------------------------------------------------------------
-- [2026_03_30_231000_deduplicate_core_profiles]
-- Deduplica perfis administrador_sistema e crie
-- ---------------------------------------------------------------------------
SET @keep_admin := (SELECT MIN(id) FROM `perfils` WHERE `nome` = 'administrador_sistema');

UPDATE `perfil_user`
SET `perfil_id` = @keep_admin
WHERE @keep_admin IS NOT NULL
  AND `perfil_id` IN (
      SELECT id
      FROM (
          SELECT id
          FROM `perfils`
          WHERE `nome` = 'administrador_sistema'
            AND id <> @keep_admin
      ) x
  );

UPDATE `perfil_regra`
SET `perfil_id` = @keep_admin
WHERE @keep_admin IS NOT NULL
  AND `perfil_id` IN (
      SELECT id
      FROM (
          SELECT id
          FROM `perfils`
          WHERE `nome` = 'administrador_sistema'
            AND id <> @keep_admin
      ) x
  );

DELETE pu1
FROM `perfil_user` pu1
JOIN `perfil_user` pu2
  ON pu1.`user_id` = pu2.`user_id`
 AND pu1.`perfil_id` = pu2.`perfil_id`
 AND (pu1.`instituicao_id` <=> pu2.`instituicao_id`)
 AND pu1.`id` > pu2.`id`;

DELETE pr1
FROM `perfil_regra` pr1
JOIN `perfil_regra` pr2
  ON pr1.`perfil_id` = pr2.`perfil_id`
 AND pr1.`regra_id` = pr2.`regra_id`
 AND pr1.`id` > pr2.`id`;

DELETE FROM `perfils`
WHERE @keep_admin IS NOT NULL
  AND `nome` = 'administrador_sistema'
  AND `id` <> @keep_admin;

SET @keep_crie := (SELECT MIN(id) FROM `perfils` WHERE `nome` = 'crie');

UPDATE `perfil_user`
SET `perfil_id` = @keep_crie
WHERE @keep_crie IS NOT NULL
  AND `perfil_id` IN (
      SELECT id
      FROM (
          SELECT id
          FROM `perfils`
          WHERE `nome` = 'crie'
            AND id <> @keep_crie
      ) x
  );

UPDATE `perfil_regra`
SET `perfil_id` = @keep_crie
WHERE @keep_crie IS NOT NULL
  AND `perfil_id` IN (
      SELECT id
      FROM (
          SELECT id
          FROM `perfils`
          WHERE `nome` = 'crie'
            AND id <> @keep_crie
      ) x
  );

DELETE pu1
FROM `perfil_user` pu1
JOIN `perfil_user` pu2
  ON pu1.`user_id` = pu2.`user_id`
 AND pu1.`perfil_id` = pu2.`perfil_id`
 AND (pu1.`instituicao_id` <=> pu2.`instituicao_id`)
 AND pu1.`id` > pu2.`id`;

DELETE pr1
FROM `perfil_regra` pr1
JOIN `perfil_regra` pr2
  ON pr1.`perfil_id` = pr2.`perfil_id`
 AND pr1.`regra_id` = pr2.`regra_id`
 AND pr1.`id` > pr2.`id`;

DELETE FROM `perfils`
WHERE @keep_crie IS NOT NULL
  AND `nome` = 'crie'
  AND `id` <> @keep_crie;

-- ---------------------------------------------------------------------------
-- [2026_04_14_191000_make_perfil_user_instituicao_nullable_for_global_admin]
-- Torna perfil_user.instituicao_id nullable para acesso global do admin sistema
-- ---------------------------------------------------------------------------
SET @perfil_user_instituicao_fk := NULL;
SELECT kcu.CONSTRAINT_NAME
INTO @perfil_user_instituicao_fk
FROM information_schema.KEY_COLUMN_USAGE kcu
WHERE kcu.TABLE_SCHEMA = DATABASE()
  AND kcu.TABLE_NAME = 'perfil_user'
  AND kcu.COLUMN_NAME = 'instituicao_id'
  AND kcu.REFERENCED_TABLE_NAME = 'instituicoes_instituicoes'
LIMIT 1;

SET @sql := IF(
    @perfil_user_instituicao_fk IS NOT NULL,
    CONCAT('ALTER TABLE `perfil_user` DROP FOREIGN KEY `', @perfil_user_instituicao_fk, '`'),
    'SELECT "FK perfil_user.instituicao_id nao encontrada para drop"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

ALTER TABLE `perfil_user`
MODIFY `instituicao_id` BIGINT UNSIGNED NULL;

SET @fk_exists := (
    SELECT COUNT(*)
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'perfil_user'
      AND COLUMN_NAME = 'instituicao_id'
      AND REFERENCED_TABLE_NAME = 'instituicoes_instituicoes'
);
SET @sql := IF(
    @fk_exists = 0,
    'ALTER TABLE `perfil_user` ADD CONSTRAINT `perfil_user_instituicao_id_foreign` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes_instituicoes`(`id`) ON DELETE SET NULL',
    'SELECT "FK perfil_user.instituicao_id ja existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `perfil_user` pu
JOIN `perfils` p ON p.`id` = pu.`perfil_id`
SET pu.`instituicao_id` = NULL
WHERE LOWER(REPLACE(REPLACE(p.`nome`, '-', ' '), '_', ' ')) IN (
    'administrador sistema',
    'administrador do sistema'
);

DELETE pu1
FROM `perfil_user` pu1
JOIN `perfil_user` pu2
  ON pu1.`user_id` = pu2.`user_id`
 AND pu1.`perfil_id` = pu2.`perfil_id`
 AND (pu1.`instituicao_id` <=> pu2.`instituicao_id`)
 AND pu1.`id` > pu2.`id`;

COMMIT;
