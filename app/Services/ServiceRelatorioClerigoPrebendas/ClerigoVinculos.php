<?php

namespace App\Services\ServiceRelatorioClerigoPrebendas;

use App\Models\InstituicoesTipoInstituicao;
use App\Traits\Identifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClerigoVinculos
{
    public function execute(array $params = []): array
    {
        $regiao = Identifiable::fetchtSessionRegiao();
        $distritoId = $params['distrito'] ?? 'all';
        $igrejaId = $params['igreja'] ?? 'all';
        $tipoVinculo = $params['tipo_vinculo'] ?? 'all';
        $onus = $params['onus'] ?? 'all';

        $clerigos = isset($params['action'])
            ? $this->fetchClerigos($regiao->id, $distritoId, $igrejaId, $tipoVinculo, $onus)
            : collect();

        return [
            'regiao' => $regiao,
            'distritos' => Identifiable::fetchDistritosByRegiao($regiao->id),
            'igrejas' => $this->fetchIgrejas($regiao->id),
            'clerigos_vinculos' => $clerigos,
            'resumo' => $this->buildResumo($clerigos),
        ];
    }

    private function fetchIgrejas(int $regiaoId): Collection
    {
        return DB::table('instituicoes_instituicoes as igreja')
            ->join('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->select('igreja.id', 'igreja.nome', 'distrito.nome as distrito_nome')
            ->where('igreja.regiao_id', $regiaoId)
            ->where('igreja.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('distrito.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('igreja.ativo', 1)
            ->where('distrito.ativo', 1)
            ->whereNull('igreja.deleted_at')
            ->whereNull('distrito.deleted_at')
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->get();
    }

    private function fetchClerigos(
        int $regiaoId,
        string $distritoId,
        string $igrejaId,
        string $tipoVinculo,
        string $onus
    ): Collection {
        $tipoVinculoSql = "CASE
            WHEN COALESCE(pf.qtd_prebendas, 0) >= 1 OR pp.data_integralizacao IS NOT NULL THEN 'Integral'
            ELSE 'Parcial'
        END";

        $query = DB::table('pessoas_nomeacoes as pn')
            ->join('pessoas_pessoas as pp', 'pp.id', '=', 'pn.pessoa_id')
            ->join('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->join('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'pn.instituicao_id')
            ->join('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'distrito.instituicao_pai_id')
            ->select(
                DB::raw('COALESCE(regiao.nome, "") as regiao_nome'),
                'distrito.nome as distrito_nome',
                'igreja.nome as igreja_nome',
                'pp.nome as clerigo_nome',
                'pf.funcao as funcao_ministerial',
                'pf.qtd_prebendas',
                DB::raw($tipoVinculoSql . ' as tipo_vinculo'),
                DB::raw("CASE WHEN COALESCE(pf.onus, 0) = 1 THEN 'Com ônus' ELSE 'Sem ônus' END as onus_descricao"),
                DB::raw("DATE_FORMAT(pp.data_integralizacao, '%d/%m/%Y') as data_integralizacao"),
                DB::raw("CASE
                    WHEN pp.telefone_preferencial IS NOT NULL AND pp.telefone_preferencial <> '' THEN pp.telefone_preferencial
                    WHEN pp.telefone_alternativo IS NOT NULL AND pp.telefone_alternativo <> '' THEN pp.telefone_alternativo
                    ELSE ''
                END as contato")
            )
            ->where('pp.tipo', 'CLE')
            ->where('pp.status_id', 1)
            ->where('igreja.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('distrito.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('igreja.ativo', 1)
            ->where('distrito.ativo', 1)
            ->whereNull('pp.deleted_at')
            ->whereNull('pn.deleted_at')
            ->whereNull('pn.data_termino')
            ->whereNull('igreja.deleted_at')
            ->whereNull('distrito.deleted_at')
            ->where(function ($query) use ($regiaoId) {
                $query->where('regiao.id', $regiaoId)
                    ->orWhere('igreja.regiao_id', $regiaoId);
            })
            ->when($distritoId !== 'all' && is_numeric($distritoId), function ($query) use ($distritoId) {
                $query->where('distrito.id', (int) $distritoId);
            })
            ->when($igrejaId !== 'all' && is_numeric($igrejaId), function ($query) use ($igrejaId) {
                $query->where('igreja.id', (int) $igrejaId);
            })
            ->when(in_array($tipoVinculo, ['integral', 'parcial'], true), function ($query) use ($tipoVinculo, $tipoVinculoSql) {
                $query->whereRaw($tipoVinculoSql . ' = ?', [ucfirst($tipoVinculo)]);
            })
            ->when(in_array($onus, ['0', '1'], true), function ($query) use ($onus) {
                $query->whereRaw('COALESCE(pf.onus, 0) = ?', [(int) $onus]);
            })
            ->orderBy('regiao.nome')
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->orderBy('pp.nome');

        return $query->get();
    }

    private function buildResumo(Collection $clerigos): array
    {
        return [
            'total' => $clerigos->count(),
            'integrais' => $clerigos->where('tipo_vinculo', 'Integral')->count(),
            'parciais' => $clerigos->where('tipo_vinculo', 'Parcial')->count(),
            'com_onus' => $clerigos->where('onus_descricao', 'Com ônus')->count(),
            'sem_onus' => $clerigos->where('onus_descricao', 'Sem ônus')->count(),
        ];
    }
}
