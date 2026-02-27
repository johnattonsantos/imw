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
                'pp.email',
                'pp.identidade',
                'pp.orgao_emissor',
                DB::raw("DATE_FORMAT(pp.data_emissao, '%d/%m/%Y') data_emissao"),
                'pp.cpf',
                DB::raw("DATE_FORMAT(pp.data_nascimento, '%d/%m/%Y') data_nascimento"),
                DB::raw("TIMESTAMPDIFF(YEAR, data_nascimento, curdate()) idade"),
                DB::raw("CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE '' END contato"),
                DB::raw("(SELECT CONCAT(iii.nome) FROM instituicoes_instituicoes iii WHERE iii.id = pp.igreja_id) igreja"),
                'pp.pais',
                'pp.uf',
                'pp.cep',
                'pp.cidade',
                'pp.bairro',
                'pp.endereco',
                'pp.numero',
                'pp.complemento',
            )
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->whereNull('pn.data_termino');
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id');//*->where('ii.ativo', '=', 1);*/
            })
            ->when($params['status'] === '0' || $params['status'] === '1', fn ($query) => $query->where('ii.ativo', $params['status']))
            ->where(['pp.status_id' => 1, 'pp.regiao_id' => $regiao])
            ->orderBy('pp.nome')
            ->groupBy('id', 'pp.nome', 'pp.data_nascimento', 'pp.telefone_preferencial', 'pp.telefone_alternativo', 'pp.igreja_id','pp.email', 'pp.identidade', 'pp.orgao_emissor', 'pp.data_emissao', 'pp.cpf','pp.pais', 'pp.uf', 'pp.cep', 'pp.cidade', 'pp.bairro', 'pp.endereco', 'pp.numero','pp.complemento')
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
