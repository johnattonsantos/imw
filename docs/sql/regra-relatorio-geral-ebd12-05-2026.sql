/*
 Relatório Geral EBD (sala/classe + professor + aluno + igreja + sede/congregação + presença)
 Data: 12/05/2026
*/

DROP VIEW IF EXISTS vw_relatorio_geral_ebd;

CREATE VIEW vw_relatorio_geral_ebd AS
SELECT
    d.id AS distrito_id,
    d.nome AS distrito_nome,
    i.id AS igreja_id,
    i.nome AS igreja_nome,

    CASE
        WHEN t.congregacao_id IS NULL THEN 'SEDE'
        ELSE 'CONGREGACAO'
    END AS tipo_unidade,

    c.id AS congregacao_id,
    c.nome AS congregacao_nome,

    cl.id AS sala_id,
    cl.nome AS sala_nome,
    cl.faixa_etaria AS sala_faixa_etaria,

    t.id AS turma_id,
    t.nome AS turma_nome,
    t.ano AS turma_ano,
    t.semestre AS turma_semestre,

    p.id AS professor_id,
    mp.id AS professor_membro_id,
    mp.nome AS professor_nome,
    mp.cpf AS professor_cpf,

    a.id AS aluno_id,
    ma.id AS aluno_membro_id,
    ma.nome AS aluno_nome,
    ma.cpf AS aluno_cpf,
    ma.status AS aluno_status_membro,
    ma.vinculo AS aluno_vinculo,

    ta.ativo AS aluno_turma_ativo,
    ta.data_entrada AS aluno_turma_data_entrada,
    ta.data_saida AS aluno_turma_data_saida,

    di.id AS diario_id,
    di.data_aula,
    di.periodo_aula,
    di.tema_aula,

    dp.id AS presenca_id,
    dp.presente AS presenca_bool,
    CASE
        WHEN dp.id IS NULL THEN 'NAO LANCADA'
        WHEN dp.presente = 1 THEN 'PRESENTE'
        ELSE 'AUSENTE'
    END AS presenca_status,
    dp.justificativa AS presenca_justificativa
FROM ebd_turma_alunos ta
INNER JOIN ebd_turmas t
    ON t.id = ta.turma_id
INNER JOIN ebd_classes cl
    ON cl.id = t.classe_id
INNER JOIN instituicoes_instituicoes i
    ON i.id = cl.igreja_id
LEFT JOIN instituicoes_instituicoes d
    ON d.id = i.instituicao_pai_id
LEFT JOIN congregacoes_congregacoes c
    ON c.id = t.congregacao_id
INNER JOIN ebd_professores p
    ON p.id = t.professor_id
INNER JOIN membresia_membros mp
    ON mp.id = p.membro_id
INNER JOIN ebd_alunos a
    ON a.id = ta.aluno_id
INNER JOIN membresia_membros ma
    ON ma.id = a.membro_id
LEFT JOIN ebd_diarios di
    ON di.turma_id = t.id
LEFT JOIN ebd_diario_presencas dp
    ON dp.diario_id = di.id
   AND dp.aluno_id = a.id
WHERE i.deleted_at IS NULL
  AND (d.id IS NULL OR d.deleted_at IS NULL)
  AND (c.id IS NULL OR c.deleted_at IS NULL)
  AND ma.deleted_at IS NULL
  AND mp.deleted_at IS NULL;

/*
 Consulta principal (aba completa):
*/
SELECT
    distrito_nome,
    igreja_nome,
    tipo_unidade,
    congregacao_nome,
    sala_nome,
    sala_faixa_etaria,
    turma_nome,
    turma_ano,
    turma_semestre,
    professor_nome,
    aluno_nome,
    aluno_cpf,
    aluno_status_membro,
    aluno_vinculo,
    data_aula,
    periodo_aula,
    tema_aula,
    presenca_status,
    presenca_justificativa
FROM vw_relatorio_geral_ebd
ORDER BY
    distrito_nome,
    igreja_nome,
    tipo_unidade,
    congregacao_nome,
    sala_nome,
    turma_ano DESC,
    turma_nome,
    aluno_nome,
    data_aula DESC;

/*
 Filtros úteis (exemplos):

 -- Apenas uma igreja
 -- WHERE igreja_id = 123

 -- Apenas congregação
 -- WHERE tipo_unidade = 'CONGREGACAO'

 -- Apenas ausentes
 -- WHERE presenca_status = 'AUSENTE'

 -- Apenas turma ativa do aluno
 -- WHERE aluno_turma_ativo = 1
*/
