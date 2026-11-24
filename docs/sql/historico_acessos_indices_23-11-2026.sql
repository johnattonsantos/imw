ALTER TABLE `gestao_acessos_sessoes`
    ADD INDEX `gestao_sessoes_login_idx` (`login_em`);
ALTER TABLE `gestao_acessos_sessao_perfis`
    ADD INDEX `gestao_perfis_regiao_instituicao_idx` (`regiao_id`, `instituicao_id`, `sessao_id`);
ALTER TABLE `audits`
    ADD INDEX `audits_login_periodo_idx` (`event`, `user_type`, `created_at`);
