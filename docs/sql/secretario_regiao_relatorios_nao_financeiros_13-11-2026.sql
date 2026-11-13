
SELECT id, nome, nivel
FROM perfils
WHERE nivel = 'R'
  AND deleted_at IS NULL
  AND nome IN ('Secretário(a) Região', 'Secretário Região', 'Secretario Região', 'Secretario Regiao');

START TRANSACTION;

INSERT INTO perfil_regra (perfil_id, regra_id, created_at, updated_at)
SELECT p.id, r.id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM perfils AS p
CROSS JOIN regras AS r
WHERE p.nivel = 'R'
  AND p.deleted_at IS NULL
  AND p.nome IN ('Secretário(a) Região', 'Secretário Região', 'Secretario Região', 'Secretario Regiao')
  AND r.deleted_at IS NULL
  AND r.nome IN (
      'regiao-menu-relatorio',
      'regiao-menu-relatorio-ebd',
      'regiao-ebd-dashboard',
      'regiao-ebd-estatisticas',
      'regiao-ebd-turmas',
      'regiao-ebd-alunos',
      'regiao-ebd-professores',
      'regiao-ebd-liderancas',
      'regiao-ebd-classes',
      'regiao-ebd-diarios',
      'regiao-ebd-agendas',
      'regiao-ebd-geral',
      'regiao-mapao',
      'regiao-relatorio-membros-ministerio',
      'regiao-relatorio-quantidade-membros',
      'regiao-menu-relatorio-conjuges',
      'regiao-menu-relatorio-congregados',
      'regiao-relatorio-perfil-membros-recebidos',
      'regiao-relatorio-perfil-membros-excluidos',
      'regiao-relatorio-estatistica-genero',
      'regiao-relatorio-ano-eclesiastico',
      'menu-relatorios-instituicoes-igrejas',
      'relatorio-clerigos-aniversariantes',
      'relatorio-clerigos-dados',
      'relatorio-clerigos-esposas',
      'relatorio-clerigos-categoria',
      'relatorio-clerigos-status',
      'relatorio-clerigos-vinculos',
      'regiao-estatistica-hitorico-nomeacoes',
      'menu-relatorios-congregacoes-igrejas',
      'regiao-relatorio-congregacoes-igrejas',
      'regiao-relatorio-aspirantes-igrejas',
      'regiao-relatorio-cnpj-igreja',
      'regiao-relatorio-contato-igreja',
      'regiao-relatorio-conta-bancaria-igreja',
      'regiao-menu-relatorio-patrimonio',
      'gceu-regiao-relatorios',
      'regiao-estatistica-gceu',
      'gceu-regiao-lista-gceu',
      'gceu-regiao-lista-funcoes',
      'gceu-regiao-lista-aniversariantes',
      'gceu-regiao-relatorio-carta-pastoral'
  )
  AND NOT EXISTS (
      SELECT 1
      FROM perfil_regra AS existente
      WHERE existente.perfil_id = p.id
        AND existente.regra_id = r.id
  );

DELETE pr
FROM perfil_regra AS pr
INNER JOIN perfils AS p ON p.id = pr.perfil_id
INNER JOIN regras AS r ON r.id = pr.regra_id
WHERE p.nivel = 'R'
  AND p.deleted_at IS NULL
  AND p.nome IN ('Secretário(a) Região', 'Secretário Região', 'Secretario Região', 'Secretario Regiao')
  AND r.nome IN (
      'regiao-menu-relatorio-financeiro',
      'regiao-relatorio-lancamento-das-igrejas',
      'regiao-ebd-lancamentodasigrejas',
      'regiao-ebd-relatorio',
      'financeiro-relatorio-balancete',
      'regiao-relatorio-saldo-das-igrejas',
      'regiao-relatorio-livro-razao-geral',
      'regiao-relatorio-orcamento',
      'regiao-relatorio-variacao-financeira',
      'financeiro-por-categoria',
      'regiao-cota-orcamentaria'
  );

COMMIT;

SELECT p.id AS perfil_id, p.nome AS perfil, r.nome AS permissao
FROM perfil_regra AS pr
INNER JOIN perfils AS p ON p.id = pr.perfil_id
INNER JOIN regras AS r ON r.id = pr.regra_id
WHERE p.nivel = 'R'
  AND p.deleted_at IS NULL
  AND p.nome IN ('Secretário(a) Região', 'Secretário Região', 'Secretario Região', 'Secretario Regiao')
  AND r.deleted_at IS NULL
ORDER BY p.id, r.nome;
