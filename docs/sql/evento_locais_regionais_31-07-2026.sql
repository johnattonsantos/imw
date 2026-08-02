

CREATE TABLE `evento_locais` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `regiao_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(180) NOT NULL,
  `endereco` varchar(180) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `evento_locais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_locais_regiao_ativo_index` (`regiao_id`,`ativo`);


ALTER TABLE `evento_locais`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `evento_locais`
  ADD CONSTRAINT `evento_locais_regiao_id_foreign` FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes` (`id`);


ALTER TABLE `eventos` ADD COLUMN `evento_local_id` bigint(20) UNSIGNED NULL AFTER `instituicao_id`