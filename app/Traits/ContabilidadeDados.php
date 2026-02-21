<?php

namespace App\Traits;

use App\Models\Mes;
use App\Models\PessoasPrebenda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ContabilidadeDados
{
    public static function fetchAnos()
    {
        if(!Auth::user()->pessoa){
            return PessoasPrebenda::select('ano')->orderBy('ano', 'desc')->groupBy('ano')->get();
        }else{
            return PessoasPrebenda::where('pessoa_id', Identifiable::fetchSessionPessoa()->id)->orderBy('ano', 'desc')->get();
        }
    }

    public static function fetchMeses()
    {
        return Mes::get();
    }

    public static function fetchMes($params)
    {
        $mes = Mes::find($params['mes']);
        return strtoupper($mes->descricao);
    }

    public static function fetchPrebandas($params)
    {
        $instituicaoId = session('session_perfil')->instituicoes->regiao->id;

        $ano = $params['ano'];
        $mes = $params['mes'];
        $prebendas = DB::select("SELECT pp2.id, pp.nome, pp.cpf, pp2.valor AS valor_prebendas, (SELECT count(*) FROM pessoas_dependentes pd WHERE pd.declarar_em_irpf = 1 AND pd.pessoa_id = pp.id) AS n_dependentes, 
        SUM(CASE WHEN (fl.plano_conta_id=41) THEN fl.valor ELSE 0 END) AS retido,
        SUM(CASE WHEN (fl.plano_conta_id=220) THEN fl.valor ELSE 0 END) AS repasse
        from pessoas_pessoas pp 
        left join pessoas_prebendas pp2 on pp.id=pp2.pessoa_id 
        left join financeiro_lancamentos fl on pp.id=fl.clerigo_id and year(fl.data_movimento)={$ano} and month(fl.data_movimento) = {$mes}
        where pp.id in 
            (select pessoa_id 
            from pessoas_nomeacoes pn ,
            instituicoes_instituicoes ii ,
            instituicoes_instituicoes ii2 
            where pn.instituicao_id=ii.id
            and ii.instituicao_pai_id=ii2.id
            and ii2.instituicao_pai_id=$instituicaoId
            and pn.data_termino is null) AND pp2.ano = {$ano}
        group by pp.nome, pp.cpf, pp2.valor, pp.id, pp2.id
        ORDER BY `pp2`.`valor` DESC");
        return $prebendas;
    }    

    public static function fetchPrebandasCotaOrcamentaria($params)
    {
        $instituicaoId = session('session_perfil')->instituicoes->regiao->id;

        $ano = $params['ano'];
        $mes = $params['mes'];
        $instituicao_id = $params['instituicao_id'];
        $prebendas = DB::select("SELECT pp2.id, pp.nome, pp.cpf, pp2.valor AS valor_prebendas, (SELECT count(*) FROM pessoas_dependentes pd WHERE pd.declarar_em_irpf = 1 AND pd.pessoa_id = pp.id) AS n_dependentes, 
        SUM(CASE WHEN (fl.plano_conta_id=41) THEN fl.valor ELSE 0 END) AS retido,
        SUM(CASE WHEN (fl.plano_conta_id=220) THEN fl.valor ELSE 0 END) AS repasse
        from pessoas_pessoas pp 
        left join pessoas_prebendas pp2 on pp.id=pp2.pessoa_id 
        left join financeiro_lancamentos fl on pp.id=fl.clerigo_id and year(fl.data_movimento)={$ano} and month(fl.data_movimento) = {$mes}
        where pp.id in 
            (select pessoa_id 
            from pessoas_nomeacoes pn ,
            instituicoes_instituicoes ii ,
            instituicoes_instituicoes ii2 
            where pn.instituicao_id = $instituicao_id
            /*where pn.instituicao_id in(2225,2587)*/
            and ii.instituicao_pai_id=ii2.id
            and ii2.instituicao_pai_id=$instituicaoId
            and pn.data_termino is null) AND pp2.ano = {$ano}
        group by pp.nome, pp.cpf, pp2.valor, pp.id, pp2.id
        ORDER BY `pp2`.`valor` DESC");
        return $prebendas;
    }   
}
