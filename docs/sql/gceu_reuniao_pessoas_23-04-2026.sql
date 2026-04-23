CREATE TABLE `gceu_reuniao_pessoas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gceu_cadastro_id` bigint(20) UNSIGNED NOT NULL,
  `instituicao_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `contato` varchar(20) DEFAULT NULL,
  `tipo` char(1) NOT NULL DEFAULT 'V' COMMENT 'V = Visitante, N = Novo Convertido',
  `data_reuniao` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `gceu_reuniao_pessoas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gceu_reuniao_pessoas_gceu` (`gceu_cadastro_id`),
  ADD KEY `idx_gceu_reuniao_pessoas_instituicao` (`instituicao_id`),
  ADD KEY `idx_gceu_reuniao_pessoas_tipo` (`tipo`),
  ADD KEY `idx_gceu_reuniao_pessoas_data` (`data_reuniao`);

ALTER TABLE `gceu_reuniao_pessoas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `gceu_reuniao_pessoas`
  ADD CONSTRAINT `fk_gceu_reuniao_pessoas_gceu`
  FOREIGN KEY (`gceu_cadastro_id`) REFERENCES `gceu_cadastros` (`id`) ON DELETE CASCADE;

