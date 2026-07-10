
CREATE TABLE `juridico_regiao_acoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `regiao_id` bigint(20) UNSIGNED NOT NULL,
  `instituicao_id` bigint(20) UNSIGNED NOT NULL,
  `advogado_causa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `advogado_oposicao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `numero_processo` varchar(120) DEFAULT NULL,
  `autor` varchar(180) NOT NULL,
  `reu` varchar(180) NOT NULL,
  `vara_tribunal` varchar(180) DEFAULT NULL,
  `advogado_oposicao_nome` varchar(180) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'em_curso',
  `resultado` varchar(30) NOT NULL DEFAULT 'sem_sentenca',
  `data_distribuicao` date DEFAULT NULL,
  `data_sentenca` date DEFAULT NULL,
  `custo_demanda` decimal(15,2) DEFAULT NULL,
  `objeto` text DEFAULT NULL,
  `teor_decisao` text DEFAULT NULL,
  `outros` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




ALTER TABLE `juridico_regiao_acoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `juridico_regiao_acoes_instituicao_id_foreign` (`instituicao_id`),
  ADD KEY `juridico_regiao_acoes_advogado_causa_id_foreign` (`advogado_causa_id`),
  ADD KEY `juridico_regiao_acoes_advogado_oposicao_id_foreign` (`advogado_oposicao_id`),
  ADD KEY `juridico_regiao_acoes_regiao_status_index` (`regiao_id`,`status`),
  ADD KEY `juridico_regiao_acoes_regiao_resultado_index` (`regiao_id`,`resultado`);


ALTER TABLE `juridico_regiao_acoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `juridico_regiao_acoes`
  ADD CONSTRAINT `juridico_regiao_acoes_advogado_causa_id_foreign` FOREIGN KEY (`advogado_causa_id`) REFERENCES `juridico_regiao_advogados` (`id`) ,
  ADD CONSTRAINT `juridico_regiao_acoes_advogado_oposicao_id_foreign` FOREIGN KEY (`advogado_oposicao_id`) REFERENCES `juridico_regiao_advogados` (`id`) ,
  ADD CONSTRAINT `juridico_regiao_acoes_instituicao_id_foreign` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ,
  ADD CONSTRAINT `juridico_regiao_acoes_regiao_id_foreign` FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ;
COMMIT;
