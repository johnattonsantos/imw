INSERT INTO `gceu_funcoes` (`id`, `funcao`) VALUES (NULL, 'Anfitrião');
INSERT INTO `gceu_funcoes` (`id`, `funcao`) VALUES (NULL, 'Samaritano');

ALTER TABLE `gceu_cadastros` CHANGE `anfitriao` `anfitriao` VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `gceu_cadastros` CHANGE `contato` `contato` VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;