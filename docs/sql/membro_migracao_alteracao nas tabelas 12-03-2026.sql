ALTER TABLE `membresia_migracao` ADD `validado` TINYINT NOT NULL DEFAULT '0' AFTER `has_errors`;
ALTER TABLE `membresia_membros` ADD `validado` TINYINT NOT NULL DEFAULT '0' AFTER `has_errors`;

INSERT INTO `perfils` (`id`, `nome`, `nivel`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'Membresia Validação', 'I', current_timestamp(), current_timestamp(), NULL);

INSERT INTO `regras` (`id`, `nome`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'membresia-validacao', current_timestamp(), current_timestamp(), NULL);

INSERT INTO perfil_regra (id, perfil_id, regra_id, created_at, updated_at)
SELECT NULL, '14', r.id, current_timestamp(), current_timestamp()
FROM regras r
WHERE r.nome = 'membresia-validacao'
ORDER BY r.id DESC
LIMIT 1;