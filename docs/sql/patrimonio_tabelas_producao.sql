

CREATE TABLE IF NOT EXISTS `patrimonio_imoveis` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo_patrimonial` VARCHAR(60) NULL,
  `natureza_imovel` VARCHAR(120) NULL,
  `nome` VARCHAR(180) NULL,
  `igreja_id` BIGINT UNSIGNED NULL COMMENT 'Se o sistema usar unidade_id, ajustar esta coluna antes de subir em producao.',
  `endereco` VARCHAR(255) NULL,
  `cidade` VARCHAR(120) NULL,
  `estado` VARCHAR(2) NULL,
  `cep` VARCHAR(9) NULL,
  `latitude` DECIMAL(10,7) NULL,
  `longitude` DECIMAL(10,7) NULL,
  `area_total` DECIMAL(15,2) NULL,
  `area_construida` DECIMAL(15,2) NULL,
  `iptu_itr` VARCHAR(120) NULL,
  `inscricao_municipal_rural` VARCHAR(180) NULL,
  `valor_historico` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_venal` DECIMAL(15,2) NULL,
  `valor_mercado` DECIMAL(15,2) NULL,
  `situacao_tributaria` VARCHAR(120) NULL,
  `cnpj_utilizado` VARCHAR(18) NULL,
  `status_titularidade` VARCHAR(80) NULL,
  `numero_matricula` VARCHAR(120) NULL,
  `cartorio` VARCHAR(180) NULL,
  `tipo_titulo` VARCHAR(120) NULL,
  `data_aquisicao_posse` DATE NULL,
  `possui_escritura_registrada` TINYINT(1) NOT NULL DEFAULT 0,
  `regularizacao_pendente` TINYINT(1) NOT NULL DEFAULT 0,
  `observacoes_juridicas` TEXT NULL,
  `avcb_validade` DATE NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patrimonio_imoveis_codigo_patrimonial_unique` (`codigo_patrimonial`),
  KEY `patrimonio_imoveis_igreja_id_foreign` (`igreja_id`),
  KEY `patrimonio_imoveis_status_titularidade_index` (`status_titularidade`),
  KEY `patrimonio_imoveis_regularizacao_pendente_index` (`regularizacao_pendente`),
  KEY `patrimonio_imoveis_avcb_validade_index` (`avcb_validade`),
  CONSTRAINT `patrimonio_imoveis_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrimonio_bens_moveis` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo_patrimonial` VARCHAR(60) NULL,
  `placa_patrimonial` VARCHAR(60) NULL,
  `nome` VARCHAR(180) NOT NULL,
  `igreja_id` BIGINT UNSIGNED NULL COMMENT 'Se o sistema usar unidade_id, ajustar esta coluna antes de subir em producao.',
  `imovel_id` BIGINT UNSIGNED NULL,
  `categoria` VARCHAR(120) NULL,
  `descricao` TEXT NULL,
  `estado_conservacao` VARCHAR(60) NULL,
  `localizacao` VARCHAR(180) NULL,
  `responsavel` VARCHAR(180) NULL,
  `data_aquisicao` DATE NULL,
  `valor_aquisicao` DECIMAL(15,2) NULL,
  `valor_residual` DECIMAL(15,2) NULL,
  `vida_util` SMALLINT UNSIGNED NULL,
  `natureza_comprobatoria` VARCHAR(120) NULL,
  `numero_documento` VARCHAR(120) NULL,
  `fornecedor_doador` VARCHAR(180) NULL,
  `status` VARCHAR(60) NOT NULL DEFAULT 'ativo',
  `observacoes` TEXT NULL,
  `qr_code_patrimonial` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patrimonio_bens_moveis_codigo_patrimonial_unique` (`codigo_patrimonial`),
  KEY `patrimonio_bens_moveis_igreja_id_foreign` (`igreja_id`),
  KEY `patrimonio_bens_moveis_imovel_id_foreign` (`imovel_id`),
  CONSTRAINT `patrimonio_bens_moveis_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`),
  CONSTRAINT `patrimonio_bens_moveis_imovel_id_foreign`
    FOREIGN KEY (`imovel_id`) REFERENCES `patrimonio_imoveis` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrimonio_documentos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(180) NULL,
  `tipo` VARCHAR(120) NULL,
  `arquivo` VARCHAR(255) NULL,
  `data_emissao` DATE NULL,
  `data_validade` DATE NULL,
  `status` VARCHAR(60) NOT NULL DEFAULT 'vigente',
  `observacoes` TEXT NULL,
  `documentavel_id` BIGINT UNSIGNED NULL,
  `documentavel_type` VARCHAR(191) NULL,
  `igreja_id` BIGINT UNSIGNED NULL COMMENT 'Se o sistema usar unidade_id, ajustar esta coluna antes de subir em producao.',
  `imovel_id` BIGINT UNSIGNED NULL,
  `bem_movel_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patrimonio_documentos_igreja_id_foreign` (`igreja_id`),
  KEY `patrimonio_documentos_imovel_id_foreign` (`imovel_id`),
  KEY `patrimonio_documentos_bem_movel_id_foreign` (`bem_movel_id`),
  KEY `patrimonio_documentos_documentavel_index` (`documentavel_type`, `documentavel_id`),
  CONSTRAINT `patrimonio_documentos_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`),
  CONSTRAINT `patrimonio_documentos_imovel_id_foreign`
    FOREIGN KEY (`imovel_id`) REFERENCES `patrimonio_imoveis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_documentos_bem_movel_id_foreign`
    FOREIGN KEY (`bem_movel_id`) REFERENCES `patrimonio_bens_moveis` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrimonio_riscos_juridicos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `igreja_id` BIGINT UNSIGNED NULL COMMENT 'Se o sistema usar unidade_id, ajustar esta coluna antes de subir em producao.',
  `imovel_id` BIGINT UNSIGNED NULL,
  `possui_onus` TINYINT(1) NOT NULL DEFAULT 0,
  `tipo_onus` VARCHAR(120) NULL,
  `descricao` TEXT NULL,
  `nivel_risco` VARCHAR(20) NOT NULL DEFAULT 'baixo',
  `data_identificacao` DATE NULL,
  `providencia_recomendada` TEXT NULL,
  `status` VARCHAR(60) NOT NULL DEFAULT 'aberto',
  `bem_movel_id` BIGINT UNSIGNED NULL,
  `documento_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patrimonio_riscos_juridicos_igreja_id_foreign` (`igreja_id`),
  KEY `patrimonio_riscos_juridicos_imovel_id_foreign` (`imovel_id`),
  KEY `patrimonio_riscos_juridicos_bem_movel_id_foreign` (`bem_movel_id`),
  KEY `patrimonio_riscos_juridicos_documento_id_foreign` (`documento_id`),
  KEY `patrimonio_riscos_nivel_risco_index` (`nivel_risco`),
  KEY `patrimonio_riscos_status_index` (`status`),
  CONSTRAINT `patrimonio_riscos_juridicos_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`),
  CONSTRAINT `patrimonio_riscos_juridicos_imovel_id_foreign`
    FOREIGN KEY (`imovel_id`) REFERENCES `patrimonio_imoveis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_riscos_juridicos_bem_movel_id_foreign`
    FOREIGN KEY (`bem_movel_id`) REFERENCES `patrimonio_bens_moveis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_riscos_juridicos_documento_id_foreign`
    FOREIGN KEY (`documento_id`) REFERENCES `patrimonio_documentos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrimonio_benfeitorias` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `igreja_id` BIGINT UNSIGNED NULL COMMENT 'Se o sistema usar unidade_id, ajustar esta coluna antes de subir em producao.',
  `imovel_id` BIGINT UNSIGNED NULL,
  `descricao` TEXT NULL,
  `data` DATE NULL,
  `valor_investido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `responsavel` VARCHAR(180) NULL,
  `documento_anexo` VARCHAR(255) NULL,
  `observacoes` TEXT NULL,
  `bem_movel_id` BIGINT UNSIGNED NULL,
  `documento_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patrimonio_benfeitorias_igreja_id_foreign` (`igreja_id`),
  KEY `patrimonio_benfeitorias_imovel_id_foreign` (`imovel_id`),
  KEY `patrimonio_benfeitorias_bem_movel_id_foreign` (`bem_movel_id`),
  KEY `patrimonio_benfeitorias_documento_id_foreign` (`documento_id`),
  KEY `patrimonio_benfeitorias_data_index` (`data`),
  CONSTRAINT `patrimonio_benfeitorias_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`),
  CONSTRAINT `patrimonio_benfeitorias_imovel_id_foreign`
    FOREIGN KEY (`imovel_id`) REFERENCES `patrimonio_imoveis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_benfeitorias_bem_movel_id_foreign`
    FOREIGN KEY (`bem_movel_id`) REFERENCES `patrimonio_bens_moveis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_benfeitorias_documento_id_foreign`
    FOREIGN KEY (`documento_id`) REFERENCES `patrimonio_documentos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrimonio_baixas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `igreja_id` BIGINT UNSIGNED NULL COMMENT 'Se o sistema usar unidade_id, ajustar esta coluna antes de subir em producao.',
  `imovel_id` BIGINT UNSIGNED NULL,
  `bem_movel_id` BIGINT UNSIGNED NULL,
  `motivo` VARCHAR(180) NULL,
  `data_baixa` DATE NULL,
  `responsavel` VARCHAR(180) NULL,
  `documento_comprobatorio` VARCHAR(255) NULL,
  `observacoes` TEXT NULL,
  `documento_id` BIGINT UNSIGNED NULL,
  `risco_juridico_id` BIGINT UNSIGNED NULL,
  `benfeitoria_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patrimonio_baixas_igreja_id_foreign` (`igreja_id`),
  KEY `patrimonio_baixas_imovel_id_foreign` (`imovel_id`),
  KEY `patrimonio_baixas_bem_movel_id_foreign` (`bem_movel_id`),
  KEY `patrimonio_baixas_documento_id_foreign` (`documento_id`),
  KEY `patrimonio_baixas_risco_juridico_id_foreign` (`risco_juridico_id`),
  KEY `patrimonio_baixas_benfeitoria_id_foreign` (`benfeitoria_id`),
  KEY `patrimonio_baixas_data_baixa_index` (`data_baixa`),
  CONSTRAINT `patrimonio_baixas_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`),
  CONSTRAINT `patrimonio_baixas_imovel_id_foreign`
    FOREIGN KEY (`imovel_id`) REFERENCES `patrimonio_imoveis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_baixas_bem_movel_id_foreign`
    FOREIGN KEY (`bem_movel_id`) REFERENCES `patrimonio_bens_moveis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_baixas_documento_id_foreign`
    FOREIGN KEY (`documento_id`) REFERENCES `patrimonio_documentos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_baixas_risco_juridico_id_foreign`
    FOREIGN KEY (`risco_juridico_id`) REFERENCES `patrimonio_riscos_juridicos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patrimonio_baixas_benfeitoria_id_foreign`
    FOREIGN KEY (`benfeitoria_id`) REFERENCES `patrimonio_benfeitorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `patrimonio_configuracoes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(40) NOT NULL,
  `nome` VARCHAR(180) NOT NULL,
  `descricao` TEXT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `ordem` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patrimonio_config_unique_nome_por_tipo` (`tipo`, `nome`),
  KEY `patrimonio_config_tipo_idx` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `patrimonio_configuracoes` (`id`, `tipo`, `nome`, `descricao`, `ativo`, `ordem`, `created_at`, `updated_at`) VALUES
(1, 'natureza', 'Terreno', 'Terreno', 1, 0, '2026-05-25 21:34:59', '2026-05-25 21:34:59'),
(2, 'natureza', 'Prédio', 'Prédio', 1, 0, '2026-05-25 21:35:07', '2026-05-25 21:35:07'),
(3, 'natureza', 'Templo', 'Templo', 1, 0, '2026-05-25 21:35:14', '2026-05-25 21:35:14'),
(4, 'natureza', 'Salão', 'Salão', 1, 0, '2026-05-25 21:35:21', '2026-05-25 21:35:21'),
(5, 'natureza', 'Casa Pastoral', 'Casa Pastoral', 1, 0, '2026-05-25 21:35:43', '2026-05-25 21:35:43'),
(6, 'natureza', 'Loja', 'Loja', 1, 0, '2026-05-25 21:35:52', '2026-05-25 21:35:52'),
(7, 'status', 'Posse Contratual', 'Posse Contratual', 1, 0, '2026-05-25 21:36:09', '2026-05-25 21:36:09'),
(8, 'status', 'Posse sem contrato', 'Posse sem contrato', 1, 0, '2026-05-25 21:36:24', '2026-05-25 21:36:24'),
(9, 'status', 'Contrato Compra e Venda', 'Contrato Compra e Venda', 1, 0, '2026-05-25 21:36:36', '2026-05-25 21:36:36'),
(10, 'status', 'Contrato de Gaveta', 'Contrato de Gaveta', 1, 0, '2026-05-25 21:36:59', '2026-05-25 21:36:59'),
(11, 'status', 'Escritura Definitiva - RGI', 'Escritura Definitiva - RGI', 1, 0, '2026-05-25 21:37:13', '2026-05-25 21:37:13'),
(12, 'iptu', 'IPTU Devedor', 'IPTU Devedor', 1, 0, '2026-05-25 21:38:00', '2026-05-25 21:38:00'),
(13, 'iptu', 'IPTU em dia', 'IPTU em dia', 1, 0, '2026-05-25 21:38:12', '2026-05-25 21:38:12'),
(14, 'iptu', 'Isenção Fiscal', 'Isenção Fiscal', 1, 0, '2026-05-25 21:38:22', '2026-05-25 21:38:22'),
(15, 'iptu', 'Anistia Fiscal', 'Anistia Fiscal', 1, 0, '2026-05-25 21:38:34', '2026-05-25 21:38:34'),
(16, 'comprobatorio', 'Nota Fiscal', 'Nota Fiscal', 1, 0, '2026-05-25 21:39:05', '2026-05-25 21:39:05'),
(17, 'comprobatorio', 'Recibo', 'Recibo', 1, 0, '2026-05-25 21:39:13', '2026-05-25 21:39:13'),
(18, 'comprobatorio', 'Declaração de Doação', 'Declaração de Doação', 1, 0, '2026-05-25 21:39:25', '2026-05-25 21:39:25'),
(19, 'tipo_documento', 'PROJETO', 'PROJETO', 1, 0, '2026-05-25 21:39:46', '2026-05-25 21:39:46'),
(20, 'tipo_documento', 'ORÇAMENTO', 'ORÇAMENTO', 1, 0, '2026-05-25 21:39:52', '2026-05-25 21:39:52'),
(21, 'tipo_documento', 'ANEXO', 'ANEXO', 1, 0, '2026-05-25 21:39:58', '2026-05-25 21:39:58');
