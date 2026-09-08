
CREATE TABLE `evento_inscricoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `origem` varchar(20) NOT NULL,
  `membro_id` char(36) DEFAULT NULL,
  `pessoa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cpf` varchar(11) NOT NULL,
  `qr_token` varchar(64) DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `funcao_eclesiastica` varchar(150) DEFAULT NULL,
  `igreja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `igreja_nome` varchar(180) DEFAULT NULL,
  `telefone` varchar(60) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `evento_inscricoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evento_inscricoes_evento_cpf_unique` (`evento_id`,`cpf`),
  ADD UNIQUE KEY `evento_inscricoes_qr_token_unique` (`qr_token`),
  ADD KEY `evento_inscricoes_pessoa_id_foreign` (`pessoa_id`),
  ADD KEY `evento_inscricoes_igreja_id_foreign` (`igreja_id`),
  ADD KEY `evento_inscricoes_evento_origem_index` (`evento_id`,`origem`),
  ADD KEY `evento_inscricoes_membro_id_foreign` (`membro_id`);


ALTER TABLE `evento_inscricoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `evento_inscricoes`
  ADD CONSTRAINT `evento_inscricoes_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_inscricoes_igreja_id_foreign` FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `evento_inscricoes_membro_id_foreign` FOREIGN KEY (`membro_id`) REFERENCES `membresia_membros` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `evento_inscricoes_pessoa_id_foreign` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas_pessoas` (`id`) ON DELETE SET NULL;
