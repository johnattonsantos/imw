

ALTER TABLE `documentos_igrejas`
  ADD COLUMN `igreja_id` BIGINT UNSIGNED NULL AFTER `regiao_id`;

ALTER TABLE `documentos_igrejas`
  ADD INDEX `documentos_igrejas_igreja_id_index` (`igreja_id`);

ALTER TABLE `documentos_igrejas`
  ADD CONSTRAINT `documentos_igrejas_igreja_id_foreign`
    FOREIGN KEY (`igreja_id`) REFERENCES `instituicoes_instituicoes` (`id`)
    ON DELETE SET NULL;



INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'documentos-igrejas-gerenciar', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '3', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'documentos-igrejas-gerenciar'
ORDER BY r.id DESC
LIMIT 1;

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '13', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'documentos-igrejas-gerenciar'
ORDER BY r.id DESC
LIMIT 1;
