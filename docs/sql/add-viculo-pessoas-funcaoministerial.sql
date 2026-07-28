ALTER TABLE `pessoas_funcaoministerial`
ADD COLUMN `vinculo` VARCHAR(20) NULL AFTER `onus`;

UPDATE `pessoas_funcaoministerial`
SET `vinculo` = 'integral'
WHERE `vinculo` IS NULL
  AND LOWER(`funcao`) LIKE '%integral%';

UPDATE `pessoas_funcaoministerial`
SET `vinculo` = 'parcial'
WHERE `vinculo` IS NULL
  AND LOWER(`funcao`) LIKE '%parcial%';