
CREATE TABLE `eventos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `instituicao_id` bigint(20) UNSIGNED NOT NULL,
  `evento_proposito_id` bigint(20) UNSIGNED DEFAULT NULL,
  `titulo` varchar(180) NOT NULL,
  `descricao` text DEFAULT NULL,
  `local` varchar(180) DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'planejado',
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `evento_equipes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `evento_funcao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `funcao` varchar(120) DEFAULT NULL,
  `contato` varchar(60) DEFAULT NULL,
  `lider` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `evento_funcoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `evento_funcoes` (`id`, `nome`, `ativo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Coordenação', 1, current_timestamp(), current_timestamp(), NULL),
(2, 'Apoio', 1, current_timestamp(), current_timestamp(), NULL),
(3, 'Recepção', 1, current_timestamp(), current_timestamp(), NULL)
(4, 'Lider', 1, current_timestamp(), current_timestamp(), NULL);

CREATE TABLE `evento_propositos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `evento_propositos` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Evangelístico', 1, current_timestamp(), current_timestamp()),
(2, 'Seminário', 1, current_timestamp(), current_timestamp()),
(3, 'Campanha', 1, current_timestamp(), current_timestamp()),
(4, 'Conferência', 1, current_timestamp(), current_timestamp()),
(5, 'Reunião de Membros', 1, current_timestamp(), current_timestamp()),
(6, 'Concílio', 1, current_timestamp(), current_timestamp()),
(7, 'Treinamento', 1, current_timestamp(), current_timestamp()),
(8, 'Culto Especial', 1, current_timestamp(), current_timestamp()),
(9, 'Ação Social', 1, current_timestamp(), current_timestamp()),
(10, 'Retiro', 1, current_timestamp(), current_timestamp()),
(11, 'Outro', 1, current_timestamp(), current_timestamp());

ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eventos_evento_proposito_id_foreign` (`evento_proposito_id`),
  ADD KEY `eventos_instituicao_data_inicio_index` (`instituicao_id`,`data_inicio`),
  ADD KEY `eventos_instituicao_status_index` (`instituicao_id`,`status`);

ALTER TABLE `evento_equipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_equipes_evento_lider_index` (`evento_id`,`lider`),
  ADD KEY `evento_equipes_evento_funcao_id_foreign` (`evento_funcao_id`);

ALTER TABLE `evento_funcoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evento_funcoes_nome_unique` (`nome`);

ALTER TABLE `evento_propositos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evento_propositos_nome_unique` (`nome`);

ALTER TABLE `eventos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `evento_equipes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `evento_funcoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `evento_propositos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_evento_proposito_id_foreign` FOREIGN KEY (`evento_proposito_id`) REFERENCES `evento_propositos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `eventos_instituicao_id_foreign` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE;

ALTER TABLE `evento_equipes`
  ADD CONSTRAINT `evento_equipes_evento_funcao_id_foreign` FOREIGN KEY (`evento_funcao_id`) REFERENCES `evento_funcoes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `evento_equipes_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

