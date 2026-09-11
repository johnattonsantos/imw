-- Modulo Comunicacao > Chat de Pastores
-- Cria conversas, participantes, mensagens, leituras e permissao do menu.

CREATE TABLE IF NOT EXISTS `comunicacao_chat_conversas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(20) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'ativa',
  `remetente_user_id` BIGINT UNSIGNED NULL,
  `remetente_pessoa_id` BIGINT UNSIGNED NOT NULL,
  `destinatario_pessoa_id` BIGINT UNSIGNED NULL,
  `igreja_id` BIGINT UNSIGNED NULL,
  `distrito_id` BIGINT UNSIGNED NULL,
  `regiao_id` BIGINT UNSIGNED NOT NULL,
  `titulo` VARCHAR(180) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `com_chat_conversas_regiao_tipo_index` (`regiao_id`, `tipo`),
  KEY `com_chat_conversas_distrito_tipo_index` (`distrito_id`, `tipo`),
  KEY `com_chat_conversas_remetente_index` (`remetente_pessoa_id`),
  KEY `com_chat_conversas_remetente_user_idx` (`remetente_user_id`),
  KEY `com_chat_conversas_destinatario_idx` (`destinatario_pessoa_id`),
  KEY `com_chat_conversas_igreja_idx` (`igreja_id`),
  CONSTRAINT `com_chat_conversas_remetente_user_fk` FOREIGN KEY (`remetente_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_conversas_remetente_pessoa_fk` FOREIGN KEY (`remetente_pessoa_id`) REFERENCES `pessoas_pessoas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `com_chat_conversas_destinatario_fk` FOREIGN KEY (`destinatario_pessoa_id`) REFERENCES `pessoas_pessoas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_conversas_igreja_fk` FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_conversas_distrito_fk` FOREIGN KEY (`distrito_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_conversas_regiao_fk` FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comunicacao_chat_participantes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversa_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `pessoa_id` BIGINT UNSIGNED NOT NULL,
  `igreja_id` BIGINT UNSIGNED NULL,
  `distrito_id` BIGINT UNSIGNED NULL,
  `regiao_id` BIGINT UNSIGNED NOT NULL,
  `lido_em` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `com_chat_participante_unique` (`conversa_id`, `pessoa_id`),
  KEY `com_chat_participantes_pessoa_lido_index` (`pessoa_id`, `lido_em`),
  KEY `com_chat_participantes_user_idx` (`user_id`),
  KEY `com_chat_participantes_igreja_idx` (`igreja_id`),
  KEY `com_chat_participantes_distrito_idx` (`distrito_id`),
  KEY `com_chat_participantes_regiao_idx` (`regiao_id`),
  CONSTRAINT `com_chat_participantes_conversa_fk` FOREIGN KEY (`conversa_id`) REFERENCES `comunicacao_chat_conversas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `com_chat_participantes_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_participantes_pessoa_fk` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas_pessoas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `com_chat_participantes_igreja_fk` FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_participantes_distrito_fk` FOREIGN KEY (`distrito_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_participantes_regiao_fk` FOREIGN KEY (`regiao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comunicacao_chat_mensagens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversa_id` BIGINT UNSIGNED NOT NULL,
  `remetente_user_id` BIGINT UNSIGNED NULL,
  `remetente_pessoa_id` BIGINT UNSIGNED NOT NULL,
  `conteudo` TEXT NOT NULL,
  `status_entrega` VARCHAR(20) NOT NULL DEFAULT 'enviada',
  `enviado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `com_chat_mensagens_conversa_enviado_index` (`conversa_id`, `enviado_em`),
  KEY `com_chat_mensagens_remetente_user_idx` (`remetente_user_id`),
  KEY `com_chat_mensagens_remetente_pessoa_idx` (`remetente_pessoa_id`),
  CONSTRAINT `com_chat_mensagens_conversa_fk` FOREIGN KEY (`conversa_id`) REFERENCES `comunicacao_chat_conversas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `com_chat_mensagens_remetente_user_fk` FOREIGN KEY (`remetente_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_mensagens_remetente_pessoa_fk` FOREIGN KEY (`remetente_pessoa_id`) REFERENCES `pessoas_pessoas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comunicacao_chat_mensagem_leituras` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mensagem_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `pessoa_id` BIGINT UNSIGNED NOT NULL,
  `lido_em` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `com_chat_mensagem_leitura_unique` (`mensagem_id`, `pessoa_id`),
  KEY `com_chat_msg_leituras_user_idx` (`user_id`),
  KEY `com_chat_msg_leituras_pessoa_idx` (`pessoa_id`),
  CONSTRAINT `com_chat_msg_leituras_mensagem_fk` FOREIGN KEY (`mensagem_id`) REFERENCES `comunicacao_chat_mensagens` (`id`) ON DELETE CASCADE,
  CONSTRAINT `com_chat_msg_leituras_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `com_chat_msg_leituras_pessoa_fk` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas_pessoas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `regras` (`nome`, `created_at`, `updated_at`, `deleted_at`)
SELECT 'comunicacao-chat-pastores', NOW(), NOW(), NULL
WHERE NOT EXISTS (
  SELECT 1 FROM `regras` WHERE `nome` = 'comunicacao-chat-pastores'
);

UPDATE `regras`
SET `deleted_at` = NULL,
    `updated_at` = NOW()
WHERE `nome` = 'comunicacao-chat-pastores';

INSERT INTO `perfil_regra` (`perfil_id`, `regra_id`, `created_at`, `updated_at`)
SELECT pr.perfil_id, regra_chat.id, NOW(), NOW()
FROM `perfil_regra` pr
JOIN `regras` regra_comunicacao ON regra_comunicacao.id = pr.regra_id
JOIN `regras` regra_chat ON regra_chat.nome = 'comunicacao-chat-pastores'
WHERE regra_comunicacao.nome = 'comunicacao'
  AND regra_comunicacao.deleted_at IS NULL
  AND NOT EXISTS (
    SELECT 1
    FROM `perfil_regra` pr2
    WHERE pr2.perfil_id = pr.perfil_id
      AND pr2.regra_id = regra_chat.id
  );

INSERT INTO `perfil_regra` (`perfil_id`, `regra_id`, `created_at`, `updated_at`)
SELECT p.id, r.id, NOW(), NOW()
FROM `perfils` p
JOIN `regras` r ON r.nome = 'comunicacao-chat-pastores'
WHERE p.nome IN ('Administrador do Sistema', 'Administrador Região', 'Administrador SRA', 'Secretário(a) Região', 'Pastor')
  AND NOT EXISTS (
    SELECT 1
    FROM `perfil_regra` pr
    WHERE pr.perfil_id = p.id
      AND pr.regra_id = r.id
  );
