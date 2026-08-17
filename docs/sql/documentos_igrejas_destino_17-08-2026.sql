-- Documentos para Igrejas: destino geral ou igreja específica
-- Geral: igreja_id NULL, todas as igrejas da região visualizam e não baixam.
-- Específico: igreja_id preenchido, apenas a igreja vinculada visualiza e baixa.

ALTER TABLE `documentos_igrejas`
  ADD COLUMN `igreja_id` BIGINT UNSIGNED NULL AFTER `regiao_id`;

ALTER TABLE `documentos_igrejas`
  ADD INDEX `documentos_igrejas_igreja_id_index` (`igreja_id`);

ALTER TABLE `documentos_igrejas`
  ADD CONSTRAINT `documentos_igrejas_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`)
    ON DELETE SET NULL;

INSERT INTO `regras` (`nome`, `created_at`, `updated_at`, `deleted_at`)
SELECT 'documentos-igrejas-gerenciar', NOW(), NOW(), NULL
WHERE NOT EXISTS (
    SELECT 1 FROM `regras` WHERE `nome` = 'documentos-igrejas-gerenciar'
);

INSERT INTO `perfil_regra` (`perfil_id`, `regra_id`, `created_at`, `updated_at`)
SELECT p.`id`, r.`id`, NOW(), NOW()
FROM `perfils` p
JOIN `regras` r ON r.`nome` = 'documentos-igrejas-gerenciar'
WHERE p.`nome` IN ('Administrador Região', 'Administrador SRA')
  AND NOT EXISTS (
      SELECT 1
      FROM `perfil_regra` pr
      WHERE pr.`perfil_id` = p.`id`
        AND pr.`regra_id` = r.`id`
  );
