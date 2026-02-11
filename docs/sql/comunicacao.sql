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


CREATE TABLE `tipo_arquivo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `extensao` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_arquivo` (`id`, `extensao`, `created_at`, `updated_at`) VALUES
(1, 'pdf', '2026-02-11 18:14:45', '2026-02-11 18:14:45'),
(2, 'doc', '2026-02-11 18:14:53', '2026-02-11 18:14:53'),
(3, 'jpg', '2026-02-11 18:14:59', '2026-02-11 18:14:59'),
(4, 'docx', '2026-02-11 18:15:03', '2026-02-11 18:15:03'),
(5, 'rar', '2026-02-11 18:15:09', '2026-02-11 18:15:09'),
(6, 'zip', '2026-02-11 18:15:13', '2026-02-11 18:15:13'),
(7, 'xls', '2026-02-11 18:16:06', '2026-02-11 18:16:06'),
(8, 'xlsx', '2026-02-11 18:16:12', '2026-02-11 18:16:12'),
(9, 'mp3', '2026-02-11 18:16:18', '2026-02-11 18:16:18'),
(10, 'mp4', '2026-02-11 18:16:23', '2026-02-11 18:16:23'),
(11, 'jpeg', '2026-02-11 18:16:31', '2026-02-11 18:16:31'),
(12, 'png', '2026-02-11 18:16:35', '2026-02-11 18:16:35'),
(13, 'gif', '2026-02-11 18:17:22', '2026-02-11 18:17:22'),
(14, 'webp', '2026-02-11 18:17:27', '2026-02-11 18:17:27');

ALTER TABLE `tipo_arquivo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_arquivo_extensao_unique` (`extensao`);

ALTER TABLE `tipo_arquivo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;