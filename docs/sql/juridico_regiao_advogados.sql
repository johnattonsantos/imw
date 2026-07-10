
CREATE TABLE `juridico_regiao_advogados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `regiao_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(180) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'causa',
  `registro_oab` varchar(60) DEFAULT NULL,
  `telefone` varchar(60) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `contatos` varchar(255) DEFAULT NULL,
  `endereco_escritorio` varchar(255) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `juridico_regiao_advogados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `juridico_regiao_advogados_regiao_tipo_index` (`regiao_id`,`tipo`);



ALTER TABLE `juridico_regiao_advogados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `juridico_regiao_advogados`
  ADD CONSTRAINT `juridico_regiao_advogados_regiao_id_foreign` FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes` (`id`);
COMMIT;

