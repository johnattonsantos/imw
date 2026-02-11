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

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'tipo-arquivo', current_timestamp(), current_timestamp(), NULL);
INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '6', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'tipo-arquivo'
ORDER BY r.id DESC
LIMIT 1;

CREATE TABLE `tipo_arquivo_comunicacao` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `instituicao_id` bigint(20) UNSIGNED NOT NULL,
  `extensao` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_arquivo_comunicacao` (`id`, `instituicao_id`, `extensao`, `created_at`, `updated_at`) VALUES
(1, 23, 'jpg', '2026-02-11 17:48:09', '2026-02-11 17:48:09'),
(2, 23, 'pdf', '2026-02-11 17:49:06', '2026-02-11 17:49:06'),
(3, 23, 'xls', '2026-02-11 17:49:41', '2026-02-11 17:49:41'),
(4, 23, 'xlsx', '2026-02-11 17:49:48', '2026-02-11 17:49:48'),
(5, 23, 'doc', '2026-02-11 17:49:55', '2026-02-11 17:49:55'),
(6, 23, 'docx', '2026-02-11 17:50:01', '2026-02-11 17:50:01'),
(7, 23, 'zip', '2026-02-11 17:50:05', '2026-02-11 17:50:05'),
(8, 23, 'rar', '2026-02-11 17:50:17', '2026-02-11 17:50:17'),
(9, 23, 'mp3', '2026-02-11 17:50:22', '2026-02-11 17:50:22'),
(10, 23, 'mp4', '2026-02-11 17:50:27', '2026-02-11 17:50:27');

ALTER TABLE `tipo_arquivo_comunicacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_arquivo_comunicacao_instituicao_extensao_unique` (`instituicao_id`,`extensao`);

ALTER TABLE `tipo_arquivo_comunicacao`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `tipo_arquivo_comunicacao`
  ADD CONSTRAINT `tipo_arquivo_comunicacao_instituicao_id_foreign` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE;