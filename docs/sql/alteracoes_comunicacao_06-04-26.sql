
CREATE TABLE `comunicacao_leituras_igrejas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `comunicacao_id` bigint(20) UNSIGNED NOT NULL,
  `igreja_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lido_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `comunicacao_leituras_igrejas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `comunicacao_leituras_igreja_unique` (`comunicacao_id`,`igreja_id`),
  ADD KEY `comunicacao_leituras_igrejas_igreja_id_foreign` (`igreja_id`),
  ADD KEY `comunicacao_leituras_igrejas_user_id_foreign` (`user_id`);

ALTER TABLE `comunicacao_leituras_igrejas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `comunicacao_leituras_igrejas`
  ADD CONSTRAINT `comunicacao_leituras_igrejas_comunicacao_id_foreign` FOREIGN KEY (`comunicacao_id`) REFERENCES `comunicacao` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comunicacao_leituras_igrejas_igreja_id_foreign` FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comunicacao_leituras_igrejas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
