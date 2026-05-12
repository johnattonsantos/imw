

CREATE TABLE `ebd_agendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(160) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime DEFAULT NULL,
  `turma_id` bigint(20) UNSIGNED DEFAULT NULL,
  `local` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ebd_alunos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `membro_id` char(36) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ebd_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `igreja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nome` varchar(120) NOT NULL,
  `faixa_etaria` varchar(120) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ebd_diarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `turma_id` bigint(20) UNSIGNED NOT NULL,
  `data_aula` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `periodo_aula` enum('manha','noite') DEFAULT NULL,
  `tema_aula` varchar(160) NOT NULL,
  `conteudo` text NOT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ebd_diario_presencas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `diario_id` bigint(20) UNSIGNED NOT NULL,
  `aluno_id` bigint(20) UNSIGNED NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT 0,
  `justificativa` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ebd_liderancas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `membro_id` char(36) NOT NULL,
  `cargo` enum('superintendente','secretario','tesoureiro') NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ebd_professores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `membro_id` char(36) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ebd_turmas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `classe_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `ano` smallint(5) UNSIGNED NOT NULL,
  `semestre` tinyint(3) UNSIGNED DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ebd_turma_alunos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `turma_id` bigint(20) UNSIGNED NOT NULL,
  `aluno_id` bigint(20) UNSIGNED NOT NULL,
  `data_entrada` date NOT NULL,
  `data_saida` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `ebd_agendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ebd_agendas_turma_id_foreign` (`turma_id`);


ALTER TABLE `ebd_alunos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ebd_alunos_membro_id_foreign` (`membro_id`);


ALTER TABLE `ebd_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ebd_classes_igreja_id_foreign` (`igreja_id`);


ALTER TABLE `ebd_diarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ebd_diarios_turma_id_foreign` (`turma_id`);


ALTER TABLE `ebd_diario_presencas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ebd_diario_presencas_diario_id_aluno_id_unique` (`diario_id`,`aluno_id`),
  ADD KEY `ebd_diario_presencas_aluno_id_foreign` (`aluno_id`);

ALTER TABLE `ebd_liderancas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ebd_liderancas_membro_id_foreign` (`membro_id`);


ALTER TABLE `ebd_professores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ebd_professores_membro_id_foreign` (`membro_id`);


ALTER TABLE `ebd_turmas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ebd_turmas_classe_id_foreign` (`classe_id`),
  ADD KEY `ebd_turmas_professor_id_foreign` (`professor_id`);


ALTER TABLE `ebd_turma_alunos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ebd_turma_alunos_aluno_id_foreign` (`aluno_id`),
  ADD KEY `ebd_turma_alunos_turma_id_aluno_id_ativo_index` (`turma_id`,`aluno_id`,`ativo`);




ALTER TABLE `ebd_agendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ebd_alunos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ebd_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ebd_diarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ebd_diario_presencas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ebd_liderancas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ebd_professores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ebd_turmas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ebd_turma_alunos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `ebd_agendas`
  ADD CONSTRAINT `ebd_agendas_turma_id_foreign` FOREIGN KEY (`turma_id`) REFERENCES `ebd_turmas` (`id`);


ALTER TABLE `ebd_alunos`
  ADD CONSTRAINT `ebd_alunos_membro_id_foreign` FOREIGN KEY (`membro_id`) REFERENCES `membresia_membros` (`id`);


ALTER TABLE `ebd_classes`
  ADD CONSTRAINT `ebd_classes_igreja_id_foreign` FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`);


ALTER TABLE `ebd_diarios`
  ADD CONSTRAINT `ebd_diarios_turma_id_foreign` FOREIGN KEY (`turma_id`) REFERENCES `ebd_turmas` (`id`);


ALTER TABLE `ebd_diario_presencas`
  ADD CONSTRAINT `ebd_diario_presencas_aluno_id_foreign` FOREIGN KEY (`aluno_id`) REFERENCES `ebd_alunos` (`id`),
  ADD CONSTRAINT `ebd_diario_presencas_diario_id_foreign` FOREIGN KEY (`diario_id`) REFERENCES `ebd_diarios` (`id`);


ALTER TABLE `ebd_liderancas`
  ADD CONSTRAINT `ebd_liderancas_membro_id_foreign` FOREIGN KEY (`membro_id`) REFERENCES `membresia_membros` (`id`);


ALTER TABLE `ebd_professores`
  ADD CONSTRAINT `ebd_professores_membro_id_foreign` FOREIGN KEY (`membro_id`) REFERENCES `membresia_membros` (`id`);


ALTER TABLE `ebd_turmas`
  ADD CONSTRAINT `ebd_turmas_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `ebd_classes` (`id`),
  ADD CONSTRAINT `ebd_turmas_professor_id_foreign` FOREIGN KEY (`professor_id`) REFERENCES `ebd_professores` (`id`);


ALTER TABLE `ebd_turma_alunos`
  ADD CONSTRAINT `ebd_turma_alunos_aluno_id_foreign` FOREIGN KEY (`aluno_id`) REFERENCES `ebd_alunos` (`id`),
  ADD CONSTRAINT `ebd_turma_alunos_turma_id_foreign` FOREIGN KEY (`turma_id`) REFERENCES `ebd_turmas` (`id`);


ALTER TABLE `ebd_classes` ADD `igreja_id` BIGINT(20) UNSIGNED NOT NULL AFTER `id`;

ALTER TABLE `ebd_classes`
  ADD KEY `ebd_classes_igreja_id_foreign` (`igreja_id`);

ALTER TABLE `ebd_classes`
  ADD CONSTRAINT `ebd_classes_igreja_id_foreign` FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`);


ALTER TABLE `ebd_turmas` ADD `congregacao_id` BIGINT(20) UNSIGNED NULL AFTER `professor_id`;

ALTER TABLE `ebd_turmas`
  ADD KEY `ebd_turmas_congregacao_id_foreign` (`congregacao_id`);

ALTER TABLE `ebd_turmas`
  ADD CONSTRAINT `ebd_turmas_congregacao_id_foreign` FOREIGN KEY (`congregacao_id`) REFERENCES `congregacoes_congregacoes` (`id`);