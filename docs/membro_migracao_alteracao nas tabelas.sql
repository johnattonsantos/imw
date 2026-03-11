ALTER TABLE `membresia_migracao` ADD `validado` TINYINT NOT NULL DEFAULT '0' AFTER `has_errors`;
ALTER TABLE `membresia_membros` ADD `validado` TINYINT NOT NULL DEFAULT '0' AFTER `has_errors`;

