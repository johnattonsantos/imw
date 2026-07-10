

CREATE TABLE `patrimonio_riscos_juridicos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `igreja_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Se o sistema usar unidade_id, ajustar esta coluna antes de subir em produção.',
  `imovel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `possui_onus` tinyint(1) NOT NULL DEFAULT 0,
  `tipo_onus` varchar(120) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `nivel_risco` varchar(20) NOT NULL DEFAULT 'baixo',
  `data_identificacao` date DEFAULT NULL,
  `providencia_recomendada` text DEFAULT NULL,
  `status` varchar(60) NOT NULL DEFAULT 'aberto',
  `bem_movel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `documento_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `patrimonio_riscos_juridicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patrimonio_riscos_juridicos_igreja_id_foreign` (`igreja_id`),
  ADD KEY `patrimonio_riscos_juridicos_imovel_id_foreign` (`imovel_id`),
  ADD KEY `patrimonio_riscos_juridicos_bem_movel_id_foreign` (`bem_movel_id`),
  ADD KEY `patrimonio_riscos_juridicos_documento_id_foreign` (`documento_id`),
  ADD KEY `patrimonio_riscos_nivel_risco_index` (`nivel_risco`),
  ADD KEY `patrimonio_riscos_status_index` (`status`);


ALTER TABLE `patrimonio_riscos_juridicos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `patrimonio_riscos_juridicos`
  ADD CONSTRAINT `patrimonio_riscos_juridicos_bem_movel_id_foreign` FOREIGN KEY (`bem_movel_id`) REFERENCES `patrimonio_bens_moveis` (`id`) ,
  ADD CONSTRAINT `patrimonio_riscos_juridicos_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `patrimonio_documentos` (`id`) ,
  ADD CONSTRAINT `patrimonio_riscos_juridicos_igreja_id_foreign` FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`),
  ADD CONSTRAINT `patrimonio_riscos_juridicos_imovel_id_foreign` FOREIGN KEY (`imovel_id`) REFERENCES `patrimonio_imoveis` (`id`) ;
COMMIT;
