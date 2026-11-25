
-- ATENCAO: a exclusao e definitiva. Faca backup das quatro tabelas primeiro.
-- Alternativa a migration 2026_11_25_120000_remove_retired_gestao_acessos_tables.
-- Nao remove audits, usuarios, perfis nem pastor_acessos_motivacionais.
DROP TABLE IF EXISTS `gestao_acessos_sessao_perfis`;
DROP TABLE IF EXISTS `gestao_acessos_sessoes`;
DROP TABLE IF EXISTS `gestao_acessos`;
DROP TABLE IF EXISTS `gestao_acessos_configuracao`;

-- Mantem o indice para a busca limitada por evento/tipo de usuario/periodo.
-- Ajuste `audits` se o nome da tabela de auditoria foi personalizado.
-- Em bases grandes, execute em janela de manutencao.
SET @imw_tem_indice = (
    SELECT COUNT(*) FROM information_schema.statistics
    WHERE table_schema = DATABASE() AND table_name = 'audits'
      AND index_name = 'audits_login_periodo_idx'
);
SET @imw_sql_indice = IF(@imw_tem_indice = 0,
    'ALTER TABLE `audits` ADD INDEX `audits_login_periodo_idx` (`event`, `user_type`, `created_at`)',
    'SELECT 1'
);
PREPARE imw_criar_indice FROM @imw_sql_indice;
EXECUTE imw_criar_indice;
DEALLOCATE PREPARE imw_criar_indice;
