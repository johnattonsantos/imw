-- Publicar tambem o codigo do relatorio. Executar no banco do ambiente desejado.
-- Apenas Administrador Regiao; nao altera Secretario Regiao ou outros perfis.
START TRANSACTION;

-- Garantir as regras de acesso e exibicao, inclusive em bancos antigos.
INSERT INTO regras (nome, created_at, updated_at, deleted_at)
SELECT desejada.nome, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL
FROM (
    SELECT 'menu-financeiro' AS nome
    UNION ALL SELECT 'menu-relatorios-financeiro'
    UNION ALL SELECT 'regiao-menu-relatorio'
    UNION ALL SELECT 'regiao-menu-relatorio-financeiro'
) AS desejada
WHERE NOT EXISTS (
    SELECT 1 FROM regras
    WHERE nome = desejada.nome AND deleted_at IS NULL
);

INSERT INTO perfil_regra (perfil_id, regra_id, created_at, updated_at)
SELECT p.id, r.id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM perfils AS p
CROSS JOIN regras AS r
WHERE p.nivel = 'R'
  AND p.deleted_at IS NULL
  AND p.nome IN ('Administrador Região', 'Administrador - Região', 'Administrador Regiao', 'Administrador - Regiao')
  AND r.deleted_at IS NULL
  AND r.nome IN ('menu-financeiro', 'menu-relatorios-financeiro', 'regiao-menu-relatorio', 'regiao-menu-relatorio-financeiro')
  AND NOT EXISTS (
      SELECT 1 FROM perfil_regra AS existente
      WHERE existente.perfil_id = p.id AND existente.regra_id = r.id
  );

COMMIT;

SELECT p.nome AS perfil, r.nome AS permissao
FROM perfils AS p
INNER JOIN perfil_regra AS pr ON pr.perfil_id = p.id
INNER JOIN regras AS r ON r.id = pr.regra_id
WHERE p.nivel = 'R'
  AND p.deleted_at IS NULL
  AND p.nome IN ('Administrador Região', 'Administrador - Região', 'Administrador Regiao', 'Administrador - Regiao')
  AND r.deleted_at IS NULL
  AND r.nome IN ('menu-financeiro', 'menu-relatorios-financeiro', 'regiao-menu-relatorio', 'regiao-menu-relatorio-financeiro')
ORDER BY p.id, r.nome;
