<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

trait ClerigoPrebenda
{
    public static function fetchClerigoAniversarinates($regiao, $params)
    {
        $clerigos = DB::table('pessoas_pessoas as pp')
            ->select(
                'pp.id as id',
                'pp.nome as nome',
                DB::raw("DATE_FORMAT(pp.data_nascimento, '%d/%m/%Y') data_nascimento"),
                DB::raw("DATE_FORMAT(pp.data_nascimento, '%d/%m') aniversario"),
                DB::raw("TIMESTAMPDIFF(YEAR, data_nascimento, curdate()) idade"),
                DB::raw("CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE '' END contato"),
                DB::raw("(SELECT CONCAT(iii.nome) FROM instituicoes_instituicoes iii WHERE iii.id = pp.igreja_id) igreja")
            )
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->whereNull('pn.data_termino');
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')->where('ii.ativo', '=', 1);
            })
            ->when($params['mes'], fn ($query) => $query->whereMonth('data_nascimento', $params['mes']))
            ->where(['pp.status_id' => 1, 'pp.regiao_id' => $regiao])
            ->orderBy('pp.nome')
            ->groupBy('id', 'pp.nome', 'pp.data_nascimento', 'pp.telefone_preferencial', 'pp.telefone_alternativo', 'pp.igreja_id')
            ->get();
        $clerigosIgrejas = [];
        foreach($clerigos as $clerigo){
            $igrejas = DB::table('pessoas_pessoas as pp')
            ->select(
                'pp.id as id',
                'pp.nome as nome',
                'ii.nome as igreja'
            )
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->whereNull('pn.data_termino');
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')->where('ii.ativo', '=', 1);
            })
            ->where(['pp.status_id' => 1, 'pp.regiao_id' => $regiao, 'pp.id' => $clerigo->id])
            ->orderBy('pp.nome')
            ->get();
            $clerigosIgrejas[] = ['clerigo' => $clerigo, 'igrejas' => $igrejas];
        }        
        return $clerigosIgrejas;
    }

    public static function fetchClerigoDados($regiao, $params)
    {
        $clerigos = DB::table('pessoas_pessoas as pp')
            ->select(
                'pp.id',
                'pp.nome',
                'ps.descricao as situacao',
                DB::raw('CONCAT(UPPER(SUBSTRING(pp.categoria, 1, 1)), SUBSTRING(pp.categoria, 2)) as categoria'),
                DB::raw("CASE pp.sexo WHEN 'M' THEN 'Masculino' WHEN 'F' THEN 'Feminino' ELSE '' END as sexo"),
                DB::raw("CASE pp.estado_civil WHEN 'S' THEN 'Solteiro' WHEN 'C' THEN 'Casado' WHEN 'D' THEN 'Divorciado' WHEN 'V' THEN 'Viúvo' ELSE '' END as estado_civil"),
                'pp.escolaridade',
                'formacoes.nivel as formacao',
                DB::raw("DATE_FORMAT(pp.data_nascimento, '%d/%m/%Y') data_nascimento"),
                DB::raw("DATE_FORMAT(pp.data_consagracao, '%d/%m/%Y') data_consagracao"),
                DB::raw("DATE_FORMAT(pp.data_ordenacao, '%d/%m/%Y') data_ordenacao"),
                DB::raw("DATE_FORMAT(pp.data_integralizacao, '%d/%m/%Y') data_integralizacao"),
                DB::raw("TIMESTAMPDIFF(YEAR, pp.data_nascimento, curdate()) idade"),
                'pp.rol',
                'pp.natural_cidade',
                'pp.natural_uf',
                'pp.nome_conjuge',
                'pp.nome_mae',
                'pp.nome_pai',
                'pp.email',
                'pp.telefone_preferencial',
                'pp.telefone_alternativo',
                DB::raw("CASE WHEN pp.telefone_preferencial IS NOT NULL AND pp.telefone_preferencial <> '' THEN pp.telefone_preferencial
                              WHEN pp.telefone_alternativo IS NOT NULL AND pp.telefone_alternativo <> '' THEN pp.telefone_alternativo
                              ELSE '' END contato"),
                'pp.identidade',
                'pp.identidade_uf',
                'pp.orgao_emissor',
                DB::raw("DATE_FORMAT(pp.data_emissao, '%d/%m/%Y') data_emissao"),
                'pp.cpf',
                DB::raw("CASE WHEN pp.residencia_propria = 1 THEN 'Sim' WHEN pp.residencia_propria = 0 THEN 'Não' ELSE '' END as residencia_propria"),
                DB::raw("CASE WHEN pp.residencia_propria_fgts = 1 THEN 'Sim' WHEN pp.residencia_propria_fgts = 0 THEN 'Não' ELSE '' END as residencia_propria_fgts"),
                'pp.pais',
                'pp.uf',
                'pp.cep',
                'pp.cidade',
                'pp.bairro',
                'pp.endereco',
                'pp.numero',
                'pp.complemento',
                'regiao.nome as regiao',
                'distrito.nome as distrito',
                'igreja.nome as igreja',
                DB::raw("GROUP_CONCAT(DISTINCT ii.nome ORDER BY ii.nome SEPARATOR ', ') as instituicoes_nomeadas"),
                DB::raw("GROUP_CONCAT(DISTINCT pf.funcao ORDER BY pf.funcao SEPARATOR ', ') as funcoes_nomeadas")
            )
            ->leftJoin('pessoas_status as ps', 'ps.id', '=', 'pp.situacao_id')
            ->leftJoin('formacoes', 'formacoes.id', '=', 'pp.formacao_id')
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'pp.regiao_id')
            ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'pp.distrito_id')
            ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'pp.igreja_id')
            ->leftJoin('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->whereNull('pn.data_termino');
            })
            ->leftJoin('instituicoes_instituicoes as ii', 'ii.id', '=', 'pn.instituicao_id')
            ->leftJoin('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->when(isset($params['status']) && $params['status'] === 'ativo', fn ($query) => $query->where('pp.status_id', 1))
            ->when(isset($params['status']) && $params['status'] === 'inativo', function ($query) {
                $query->where(function ($query) {
                    $query->where('pp.status_id', '<>', 1)
                        ->orWhereNull('pp.status_id');
                });
            })
            ->where('pp.regiao_id', $regiao)
            ->orderBy('pp.nome')
            ->groupBy(
                'pp.id',
                'pp.nome',
                'ps.descricao',
                'pp.categoria',
                'pp.sexo',
                'pp.estado_civil',
                'pp.escolaridade',
                'formacoes.nivel',
                'pp.data_nascimento',
                'pp.data_consagracao',
                'pp.data_ordenacao',
                'pp.data_integralizacao',
                'pp.rol',
                'pp.natural_cidade',
                'pp.natural_uf',
                'pp.nome_conjuge',
                'pp.nome_mae',
                'pp.nome_pai',
                'pp.email',
                'pp.telefone_preferencial',
                'pp.telefone_alternativo',
                'pp.identidade',
                'pp.identidade_uf',
                'pp.orgao_emissor',
                'pp.data_emissao',
                'pp.cpf',
                'pp.residencia_propria',
                'pp.residencia_propria_fgts',
                'pp.pais',
                'pp.uf',
                'pp.cep',
                'pp.cidade',
                'pp.bairro',
                'pp.endereco',
                'pp.numero',
                'pp.complemento',
                'regiao.nome',
                'distrito.nome',
                'igreja.nome'
            )
            ->get();
        return $clerigos;
    }

    public static function fetchClerigoCategoria($regiao, $params)
    {
        $clerigos = DB::table('pessoas_pessoas as pp')
            ->select(
                'pp.id as id',
                'pp.nome',
                DB::raw('CONCAT(UPPER(SUBSTRING(pp.categoria, 1, 1)), SUBSTRING(pp.categoria, 2)) as categoria'),
                DB::raw("CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE '' END contato"),
                DB::raw("(SELECT CONCAT(iii.nome) FROM instituicoes_instituicoes iii WHERE iii.id = pp.igreja_id AND iii.ativo = 1) igreja")
            )
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id');
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')->where('ii.ativo', '=', 1);
            })
            ->where(['pp.status_id' => 1, 'ii.ativo' => 1, 'pp.regiao_id' => $regiao])->whereNull('pn.data_termino')
            ->when($params['categoria'], fn ($query) => $query->where('pp.categoria', $params['categoria']))
            ->orderBy('pp.nome')
            ->groupBy('id', 'pp.nome', 'pp.telefone_preferencial', 'pp.telefone_alternativo', 'pp.igreja_id', 'pp.categoria')
            ->get();        
        return  $clerigos;
    }

    public static function fetchClerigoStatus($regiao, $params)
    {
        $clerigos = DB::table('pessoas_pessoas as pp')
            ->select(
                'pp.id as id',
                'pp.nome',
                'ps.descricao as status',
                DB::raw("CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE '' END contato"),
                DB::raw("(SELECT CONCAT(iii.nome) FROM instituicoes_instituicoes iii WHERE iii.id = pp.igreja_id AND iii.ativo = 1) igreja")
            )
            ->Join('pessoas_status as ps', function ($join) {
                $join->on('ps.id', '=', 'pp.status_id');
            })
            ->leftJoin('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id');
            })
            ->leftJoin('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id');
            })
            ->when($params['status'], fn ($query) => $query->where('pp.status_id', $params['status']))
            ->where([/*'pp.status_id' => 1,*/ 'pp.regiao_id' => $regiao,  'ii.ativo' => 1])->whereNull('pn.data_termino')
            ->orderBy('pp.nome')
            ->groupBy('id', 'pp.nome', 'pp.telefone_preferencial', 'pp.telefone_alternativo', 'pp.igreja_id', 'ps.descricao')
            ->get();        
        return  $clerigos;
    }

        public static function fetchClerigoAniversarinatesDistrito($distrito, $params)
    {
        $clerigos = DB::table('pessoas_pessoas as pp')
            ->select(
                'pp.id as id',
                'pp.nome as nome',
                DB::raw("DATE_FORMAT(pp.data_nascimento, '%d/%m/%Y') data_nascimento"),
                DB::raw("DATE_FORMAT(pp.data_nascimento, '%d/%m') aniversario"),
                DB::raw("TIMESTAMPDIFF(YEAR, data_nascimento, curdate()) idade"),
                DB::raw("CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE '' END contato"),
                DB::raw("(SELECT CONCAT(iii.nome) FROM instituicoes_instituicoes iii WHERE iii.id = pp.igreja_id) igreja")
            )
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->whereNull('pn.data_termino');
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')->where('ii.ativo', '=', 1);
            })
            ->when($params['mes'], fn ($query) => $query->whereMonth('data_nascimento', $params['mes']))
            ->where(['pp.status_id' => 1, 'pp.distrito_id' => $distrito])
            ->orderBy('pp.nome')
            ->groupBy('id', 'pp.nome', 'pp.data_nascimento', 'pp.telefone_preferencial', 'pp.telefone_alternativo', 'pp.igreja_id')
            ->get();
        $clerigosIgrejas = [];
        foreach($clerigos as $clerigo){
            $igrejas = DB::table('pessoas_pessoas as pp')
            ->select(
                'pp.id as id',
                'pp.nome as nome',
                'ii.nome as igreja'
            )
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->whereNull('pn.data_termino');
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')->where('ii.ativo', '=', 1);
            })
            ->where(['pp.status_id' => 1, 'pp.distrito_id' => $distrito, 'pp.id' => $clerigo->id])
            ->orderBy('pp.nome')
            ->get();
            $clerigosIgrejas[] = ['clerigo' => $clerigo, 'igrejas' => $igrejas];
        }        
        return $clerigosIgrejas;
    }

}
