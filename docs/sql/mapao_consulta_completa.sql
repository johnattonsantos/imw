-- Consulta completa do Mapao Regional
-- Troque o valor de @regiao_id pelo ID da regiao desejada.

SET @regiao_id = 23;
SET @data_inicial = STR_TO_DATE(CONCAT(YEAR(CURDATE()) - 1, '-11-01'), '%Y-%m-%d');
SET @data_final = CURDATE();

SELECT
    -- Membresia
    (
        SELECT COUNT(*)
        FROM membresia_membros mm
        WHERE mm.regiao_id = @regiao_id
          AND mm.status = 'A'
          AND mm.vinculo = 'M'
          AND mm.deleted_at IS NULL
    ) AS total_membros,

    (
        SELECT COUNT(*)
        FROM pessoas_pessoas pp
        WHERE pp.regiao_id = @regiao_id
          AND pp.situacao_id = 1
          AND LOWER(pp.categoria) IN ('ministro', 'pastor', 'missionária', 'missionaria')
          AND pp.deleted_at IS NULL
    ) AS total_clerigos,

    (
        SELECT COUNT(*)
        FROM membresia_membros mm
        WHERE mm.regiao_id = @regiao_id
          AND mm.status = 'A'
          AND mm.vinculo = 'C'
          AND mm.deleted_at IS NULL
    ) AS total_congregados,

    -- Instituicoes
    (
        SELECT COUNT(*)
        FROM instituicoes_instituicoes di
        WHERE di.instituicao_pai_id = @regiao_id
          AND di.ativo = 1
          AND di.deleted_at IS NULL
    ) AS total_distritos,

    (
        SELECT COUNT(*)
        FROM instituicoes_instituicoes ig
        JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
        WHERE di.instituicao_pai_id = @regiao_id
          AND ig.tipo_instituicao_id = 1
          AND ig.ativo = 1
          AND ig.deleted_at IS NULL
          AND di.ativo = 1
          AND di.deleted_at IS NULL
    ) AS total_igrejas,

    (
        SELECT COUNT(*)
        FROM congregacoes_congregacoes cc
        JOIN instituicoes_instituicoes ig ON ig.id = cc.instituicao_id
        JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
        WHERE di.instituicao_pai_id = @regiao_id
          AND ig.tipo_instituicao_id = 1
          AND ig.ativo = 1
          AND ig.deleted_at IS NULL
          AND di.ativo = 1
          AND di.deleted_at IS NULL
          AND cc.ativo = 1
          AND cc.deleted_at IS NULL
    ) AS total_congregacoes,

    -- Financeiro
    (
        SELECT COALESCE(SUM(fl.valor), 0)
        FROM financeiro_lancamentos fl
        WHERE fl.deleted_at IS NULL
          AND fl.tipo_lancamento = 'E'
          AND (fl.estornado = 0 OR fl.estornado IS NULL)
          AND fl.data_movimento BETWEEN @data_inicial AND @data_final
          AND (
              fl.hist_regiao_id = @regiao_id
              OR fl.instituicao_id = @regiao_id
              OR fl.instituicao_id IN (
                  SELECT di.id
                  FROM instituicoes_instituicoes di
                  WHERE di.instituicao_pai_id = @regiao_id
                    AND di.ativo = 1
                    AND di.deleted_at IS NULL
              )
              OR fl.instituicao_id IN (
                  SELECT ig.id
                  FROM instituicoes_instituicoes ig
                  JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
                  WHERE di.instituicao_pai_id = @regiao_id
                    AND ig.tipo_instituicao_id = 1
                    AND ig.ativo = 1
                    AND ig.deleted_at IS NULL
                    AND di.ativo = 1
                    AND di.deleted_at IS NULL
              )
          )
    ) AS total_arrecadacao_bienio,

    (
        SELECT COALESCE(SUM(fl.valor), 0) /
               GREATEST(
                   1,
                   TIMESTAMPDIFF(
                       MONTH,
                       DATE_FORMAT(@data_inicial, '%Y-%m-01'),
                       DATE_FORMAT(@data_final, '%Y-%m-01')
                   ) + 1
               )
        FROM financeiro_lancamentos fl
        WHERE fl.deleted_at IS NULL
          AND fl.tipo_lancamento = 'E'
          AND (fl.estornado = 0 OR fl.estornado IS NULL)
          AND fl.data_movimento BETWEEN @data_inicial AND @data_final
          AND (
              fl.hist_regiao_id = @regiao_id
              OR fl.instituicao_id = @regiao_id
              OR fl.instituicao_id IN (
                  SELECT di.id
                  FROM instituicoes_instituicoes di
                  WHERE di.instituicao_pai_id = @regiao_id
                    AND di.ativo = 1
                    AND di.deleted_at IS NULL
              )
              OR fl.instituicao_id IN (
                  SELECT ig.id
                  FROM instituicoes_instituicoes ig
                  JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
                  WHERE di.instituicao_pai_id = @regiao_id
                    AND ig.tipo_instituicao_id = 1
                    AND ig.ativo = 1
                    AND ig.deleted_at IS NULL
                    AND di.ativo = 1
                    AND di.deleted_at IS NULL
              )
          )
    ) AS media_arrecadacao_mensal,

    -- GCEU
    (
        SELECT COUNT(*)
        FROM gceu_cadastros gc
        JOIN instituicoes_instituicoes ig ON ig.id = gc.instituicao_id
        JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
        WHERE di.instituicao_pai_id = @regiao_id
          AND gc.status = 'A'
          AND gc.deleted_at IS NULL
          AND ig.tipo_instituicao_id = 1
          AND ig.ativo = 1
          AND ig.deleted_at IS NULL
          AND di.ativo = 1
          AND di.deleted_at IS NULL
    ) AS total_gceus,

    (
        SELECT COUNT(DISTINCT gm.membro_id)
        FROM gceu_membros gm
        JOIN gceu_cadastros gc ON gc.id = gm.gceu_cadastro_id
        JOIN instituicoes_instituicoes ig ON ig.id = gc.instituicao_id
        JOIN instituicoes_instituicoes di ON di.id = ig.instituicao_pai_id
        WHERE di.instituicao_pai_id = @regiao_id
          AND gc.status = 'A'
          AND gc.deleted_at IS NULL
          AND gm.deleted_at IS NULL
          AND ig.tipo_instituicao_id = 1
          AND ig.ativo = 1
          AND ig.deleted_at IS NULL
          AND di.ativo = 1
          AND di.deleted_at IS NULL
    ) AS total_integrantes_gceus,

    -- Ministerios
    (
        SELECT COUNT(*)
        FROM membresia_setores ms
        WHERE ms.deleted_at IS NULL
    ) AS total_ministerios,

    (
        SELECT COUNT(DISTINCT mfm.membro_id)
        FROM membresia_funcoesministeriais mfm
        JOIN membresia_membros mm ON mm.id = mfm.membro_id
        WHERE mm.regiao_id = @regiao_id
          AND mm.status = 'A'
          AND mm.vinculo = 'M'
          AND mm.deleted_at IS NULL
          AND mfm.deleted_at IS NULL
          AND mfm.data_saida IS NULL
    ) AS total_integrantes_ministerios,

    -- Rol permanente
    (
        SELECT COUNT(*)
        FROM membresia_rolpermanente mr
        WHERE mr.regiao_id = @regiao_id
          AND mr.status = 'A'
          AND mr.deleted_at IS NULL
          AND mr.dt_recepcao BETWEEN @data_inicial AND @data_final
    ) AS total_recebimentos_bienio,

    (
        SELECT COUNT(*) /
               GREATEST(
                   1,
                   (
                       (YEAR(@data_final) * 4) + CEIL(MONTH(@data_final) / 3)
                   ) -
                   (
                       (YEAR(@data_inicial) * 4) + CEIL(MONTH(@data_inicial) / 3)
                   ) + 1
               )
        FROM membresia_rolpermanente mr
        WHERE mr.regiao_id = @regiao_id
          AND mr.status = 'A'
          AND mr.deleted_at IS NULL
          AND mr.dt_recepcao BETWEEN @data_inicial AND @data_final
    ) AS media_recebimento_membros_trimestre,

    (
        SELECT COUNT(*)
        FROM membresia_rolpermanente mr
        WHERE mr.regiao_id = @regiao_id
          AND mr.status = 'I'
          AND mr.deleted_at IS NULL
          AND mr.dt_exclusao BETWEEN @data_inicial AND @data_final
    ) AS total_exclusoes_bienio,

    (
        SELECT COUNT(*) /
               GREATEST(
                   1,
                   (
                       (YEAR(@data_final) * 4) + CEIL(MONTH(@data_final) / 3)
                   ) -
                   (
                       (YEAR(@data_inicial) * 4) + CEIL(MONTH(@data_inicial) / 3)
                   ) + 1
               )
        FROM membresia_rolpermanente mr
        WHERE mr.regiao_id = @regiao_id
          AND mr.status = 'I'
          AND mr.deleted_at IS NULL
          AND mr.dt_exclusao BETWEEN @data_inicial AND @data_final
    ) AS media_exclusoes_trimestre,

    -- Periodo usado
    @data_inicial AS data_inicial,
    @data_final AS data_final;
