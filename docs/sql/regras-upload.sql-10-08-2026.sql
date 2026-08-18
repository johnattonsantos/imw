INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'documentos-igrejas-gerenciar', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'documentos-igrejas-gerenciar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'documentos-igrejas-gerenciar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'documentos-igrejas-visualizar', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'documentos-igrejas-visualizar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '4', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'documentos-igrejas-visualizar'
ORDER BY r.id DESC
LIMIT 1;

CREATE TABLE IF NOT EXISTS `documentos_igrejas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `regiao_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `documentos_igrejas_regiao_id_index` (`regiao_id`),
  CONSTRAINT `documentos_igrejas_regiao_id_foreign`
    FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes` (`id`),
  CONSTRAINT `documentos_igrejas_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE IF NOT EXISTS `documentos_igrejas_arquivos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `documento_igreja_id` BIGINT UNSIGNED NOT NULL,
  `nome_original` VARCHAR(255) NOT NULL,
  `caminho` VARCHAR(500) NOT NULL,
  `mime_type` VARCHAR(120) NULL,
  `tamanho` BIGINT UNSIGNED NULL,
  `ordem` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `documentos_igrejas_arquivos_documento_igreja_id_index` (`documento_igreja_id`),
  CONSTRAINT `documentos_igrejas_arquivos_documento_igreja_id_foreign`
    FOREIGN KEY (`documento_igreja_id`) REFERENCES `documentos_igrejas` (`id`) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci';
