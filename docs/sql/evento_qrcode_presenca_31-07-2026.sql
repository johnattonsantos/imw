ALTER TABLE `evento_inscricoes` ADD COLUMN `qr_token` varchar(64) COLLATE utf8mb4_unicode_ci NULL AFTER `cpf`

ALTER TABLE `evento_inscricoes` ADD UNIQUE KEY `evento_inscricoes_qr_token_unique` (`qr_token`)


CREATE TABLE `evento_inscricao_movimentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evento_inscricao_id` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `registrado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `registrado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacoes` varchar(180) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `evento_inscricao_movimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_inscricao_movimentos_registrado_por_foreign` (`registrado_por`),
  ADD KEY `evento_movimentos_evento_registrado_index` (`evento_id`,`registrado_em`),
  ADD KEY `evento_movimentos_inscricao_registrado_index` (`evento_inscricao_id`,`registrado_em`);


ALTER TABLE `evento_inscricao_movimentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `evento_inscricao_movimentos`
  ADD CONSTRAINT `evento_inscricao_movimentos_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_inscricao_movimentos_evento_inscricao_id_foreign` FOREIGN KEY (`evento_inscricao_id`) REFERENCES `evento_inscricoes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_inscricao_movimentos_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL;

