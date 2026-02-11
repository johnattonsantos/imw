INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'categoria-comunicacao',  current_timestamp(),  current_timestamp(), NULL);
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'categoria-comunicacao'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'comunicacao',  current_timestamp(),  current_timestamp(), NULL);
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'comunicacao'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '7', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'comunicacao'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'comunicacao-editar',  current_timestamp(),  current_timestamp(), NULL);
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'comunicacao-editar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'comunicacao-deletar',  current_timestamp(),  current_timestamp(), NULL);
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'comunicacao-deletar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'comunicacao-novo',  current_timestamp(),  current_timestamp(), NULL);
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'comunicacao-novo'
ORDER BY r.id DESC
LIMIT 1;


CREATE TABLE `categoria_comunicacao` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `instituicao_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `categoria_comunicacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categoria_comunicacao_instituicao_nome_unique` (`instituicao_id`,`nome`);

ALTER TABLE `categoria_comunicacao`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `categoria_comunicacao`
  ADD CONSTRAINT `categoria_comunicacao_instituicao_id_foreign` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE;
COMMIT;


CREATE TABLE `comunicacao` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `instituicao_id` bigint(20) UNSIGNED NOT NULL,
  `categoria_comunicacao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `titulo` varchar(191) NOT NULL,
  `comentario` text NOT NULL,
  `arquivo` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `comunicacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comunicacao_instituicao_id_foreign` (`instituicao_id`),
  ADD KEY `comunicacao_categoria_comunicacao_id_foreign` (`categoria_comunicacao_id`);

ALTER TABLE `comunicacao`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `comunicacao`
  ADD CONSTRAINT `comunicacao_categoria_comunicacao_id_foreign` FOREIGN KEY (`categoria_comunicacao_id`) REFERENCES `categoria_comunicacao` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comunicacao_instituicao_id_foreign` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE;
COMMIT;


INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'tipo-arquivo', current_timestamp(), current_timestamp(), NULL);
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '6', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'tipo-arquivo'
ORDER BY r.id DESC
LIMIT 1;


CREATE TABLE `tipo_arquivo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `extensao` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_arquivo` (`id`, `extensao`, `created_at`, `updated_at`) VALUES
(1, 'pdf', current_timestamp(), current_timestamp()),
(2, 'doc', current_timestamp(), current_timestamp()),
(3, 'jpg', current_timestamp(), current_timestamp()),
(4, 'docx', current_timestamp(), current_timestamp()),
(5, 'rar', current_timestamp(), current_timestamp()),
(6, 'zip', current_timestamp(), current_timestamp()),
(7, 'xls', current_timestamp(), current_timestamp()),
(8, 'xlsx', current_timestamp(), current_timestamp()),
(9, 'mp3', current_timestamp(), current_timestamp()),
(10, 'mp4', current_timestamp(), current_timestamp()),
(11, 'jpeg', current_timestamp(), current_timestamp()),
(12, 'png', current_timestamp(), current_timestamp()),
(13, 'gif', current_timestamp(), current_timestamp()),
(14, 'webp', current_timestamp(), current_timestamp());

ALTER TABLE `tipo_arquivo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_arquivo_extensao_unique` (`extensao`);

ALTER TABLE `tipo_arquivo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
