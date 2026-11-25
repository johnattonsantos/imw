<?php

namespace App\Services\ServiceRegiaoRelatorios;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GestaoAcessosService
{
    public function consultaSessoes(int $regiaoId, string $inicio, string $fim, string $usuario = '', ?int $instituicaoId = null)
    {
        $limite = Carbon::parse($fim)->addDay()->startOfDay()->toDateTimeString();
        $instituicoes = $this->instituicoesDaRegiao($regiaoId);
        $vinculos = $this->vinculosAtuaisQuery($regiaoId, $instituicaoId)
            ->select('v.user_id')
            ->selectRaw("MIN(vi.nome) as instituicao,
                CASE WHEN COUNT(DISTINCT v.instituicao_id) = 1 THEN MIN(vp.nome)
                ELSE MIN(CONCAT(vi.nome, ': ', vp.nome)) END as perfil")
            ->groupBy('v.user_id');
        // Aggregate before joining so multiple current profiles never duplicate an audit login.
        $perfisAtuais = DB::table('perfil_user as pu')
            ->join('perfils as p', 'p.id', '=', 'pu.perfil_id')
            ->join('instituicoes_instituicoes as pi', 'pi.id', '=', 'pu.instituicao_id')
            ->whereNull('pi.deleted_at')->where('pi.ativo', 1)
            ->whereNull('p.deleted_at')->whereIn('pu.instituicao_id', clone $instituicoes)
            ->select('pu.user_id', 'pu.instituicao_id')
            ->selectRaw('COUNT(DISTINCT pu.perfil_id) as quantidade, MIN(p.nome) as nome')
            ->groupBy('pu.user_id', 'pu.instituicao_id');

        $logins = DB::table(config('audit.drivers.database.table', 'audits').' as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('instituicoes_instituicoes as i', 'i.id', '=', 'a.instituicao_id')
            ->leftJoinSub($vinculos, 'vinculos', 'vinculos.user_id', '=', 'a.user_id')
            ->leftJoinSub($perfisAtuais, 'atual', function ($join) {
                $join->on('atual.user_id', '=', 'a.user_id')->on('atual.instituicao_id', '=', 'a.instituicao_id');
            })
            ->where('a.event', 'login')->where('a.user_type', User::class)
            ->whereNotNull('a.created_at')
            ->where('a.created_at', '>=', $inicio.' 00:00:00')->where('a.created_at', '<', $limite)
            ->when($instituicaoId, function ($q) use ($regiaoId, $instituicaoId) {
                $q->where(function ($q) use ($regiaoId, $instituicaoId) {
                    $q->where('a.instituicao_id', $instituicaoId)
                        ->orWhere(function ($q) use ($regiaoId, $instituicaoId) {
                            $q->whereNull('a.instituicao_id')->addWhereExistsQuery(
                                $this->vinculosAtuaisQuery($regiaoId, $instituicaoId)
                                    ->selectRaw('1')->whereColumn('v.user_id', 'a.user_id')
                            );
                        });
                });
            })
            ->where(function ($q) use ($instituicoes) {
                $q->whereIn('a.instituicao_id', clone $instituicoes)
                    ->orWhere(function ($q) use ($instituicoes) {
                        // Unscoped historical logins may only be shown for users exclusive to this region.
                        $q->whereNull('a.instituicao_id')
                            ->whereExists(function ($q) use ($instituicoes) {
                                $q->selectRaw('1')->from('perfil_user as local')
                                    ->whereColumn('local.user_id', 'a.user_id')
                                    ->whereIn('local.instituicao_id', clone $instituicoes);
                            })->whereNotExists(function ($q) use ($instituicoes) {
                                $q->selectRaw('1')->from('perfil_user as outro')
                                    ->whereColumn('outro.user_id', 'a.user_id')
                                    ->where(function ($q) use ($instituicoes) {
                                        $q->whereNull('outro.instituicao_id')
                                            ->orWhereNotIn('outro.instituicao_id', clone $instituicoes);
                                    });
                            });
                    });
            })
            ->selectRaw("CASE
                WHEN a.instituicao_id IS NULL THEN COALESCE(vinculos.instituicao, 'Sem instituição atual vinculada')
                ELSE COALESCE(i.nome, 'Instituição não localizada')
                END as instituicao")
            ->addSelect('u.name as usuario')
            ->selectRaw("CASE
                WHEN a.instituicao_id IS NULL THEN COALESCE(vinculos.perfil, 'Sem perfil atual vinculado')
                WHEN atual.quantidade >= 1 THEN atual.nome
                ELSE 'Sem perfil atual vinculado'
                END as perfil")
            ->addSelect('a.created_at as primeiro', 'a.id as ordenacao_id', 'a.user_id', 'a.instituicao_id as audit_instituicao_id');

        if (trim($usuario) !== '') {
            $logins->whereRaw("u.name LIKE ? ESCAPE '!'", [$this->termoLike($usuario)]);
        }

        return DB::query()->fromSub($logins, 'acessos');
    }

    public function sessoes(int $regiaoId, string $inicio, string $fim, string $usuario = '')
    {
        return $this->consultaSessoes($regiaoId, $inicio, $fim, $usuario)
            ->orderByDesc('primeiro')->orderByDesc('ordenacao_id')
            ->paginate(50)->withQueryString();
    }

    public function datatable(int $regiaoId, array $filtros, array $tabela): array
    {
        $query = $this->consultaSessoes($regiaoId, $filtros['data_inicial'], $filtros['data_final'],
            trim($filtros['usuario'] ?? ''), isset($filtros['instituicao_id']) ? (int) $filtros['instituicao_id'] : null);
        $total = (clone $query)->count();
        $busca = trim(data_get($tabela, 'search.value', '') ?? '');
        if ($busca !== '') {
            $query->where(function ($q) use ($busca, $regiaoId, $filtros) {
                foreach (['instituicao', 'usuario', 'perfil'] as $coluna) {
                    $q->orWhereRaw("acessos.$coluna LIKE ? ESCAPE '!'", [$this->termoLike($busca)]);
                }
                $q->orWhereRaw("DATE_FORMAT(acessos.primeiro, '%d/%m/%Y %H:%i:%s') LIKE ? ESCAPE '!'", [$this->termoLike($busca)]);
                $q->addWhereExistsQuery($this->vinculosAtuaisQuery($regiaoId, !empty($filtros['instituicao_id']) ? (int) $filtros['instituicao_id'] : null)
                    ->selectRaw('1')->whereColumn('v.user_id', 'acessos.user_id')
                    ->whereRaw('(acessos.audit_instituicao_id IS NULL OR v.instituicao_id = acessos.audit_instituicao_id)')
                    ->where(function ($vinculo) use ($busca) {
                        $vinculo->whereRaw("vi.nome LIKE ? ESCAPE '!'", [$this->termoLike($busca)])
                            ->orWhereRaw("vp.nome LIKE ? ESCAPE '!'", [$this->termoLike($busca)]);
                    }), 'or');
            });
        }
        $filtrado = $busca === '' ? $total : (clone $query)->count();
        $colunas = ['instituicao', 'usuario', 'perfil', 'primeiro'];
        $coluna = $colunas[(int) data_get($tabela, 'order.0.column', 3)] ?? 'primeiro';
        $direcao = data_get($tabela, 'order.0.dir') === 'asc' ? 'asc' : 'desc';
        $linhas = $query->orderBy($coluna, $direcao)->orderByDesc('ordenacao_id')
            ->offset(max(0, (int) ($tabela['start'] ?? 0)))
            ->limit(min(100, max(10, (int) ($tabela['length'] ?? 25))))->get();
        // Fetch complete links only for this page, without multiplying the audit rows.
        $usuarios = $linhas->pluck('user_id')->filter()->unique()->values()->all();
        $atuais = empty($usuarios) ? collect() : $this->vinculosAtuaisQuery($regiaoId, !empty($filtros['instituicao_id']) ? (int) $filtros['instituicao_id'] : null)
            ->whereIn('v.user_id', $usuarios)
            ->select('v.user_id', 'v.instituicao_id', 'vi.nome as instituicao', 'vp.nome as perfil')
            ->distinct()->orderBy('vi.nome')->orderBy('vp.nome')->get()->groupBy('user_id');

        return ['draw' => (int) ($tabela['draw'] ?? 0), 'recordsTotal' => $total, 'recordsFiltered' => $filtrado,
            'data' => $linhas->map(function ($linha) use ($atuais) {
                $vinculos = $atuais->get(data_get($linha, 'user_id'), collect());
                if (data_get($linha, 'audit_instituicao_id') !== null) {
                    $vinculos = $vinculos->where('instituicao_id', $linha->audit_instituicao_id);
                }
                $grupos = $vinculos->groupBy('instituicao_id');
                if ($grupos->isNotEmpty()) {
                    if (data_get($linha, 'audit_instituicao_id') === null) {
                        $linha->instituicao = $grupos->map(fn ($grupo) => $grupo->first()->instituicao)->implode('; ').' (vínculos atuais)';
                    }
                    $linha->perfil = $grupos->map(function ($grupo) use ($grupos) {
                        $perfis = $grupo->pluck('perfil')->unique()->implode(', ');
                        return $grupos->count() > 1 ? $grupo->first()->instituicao.': '.$perfis : $perfis;
                    })->implode('; ');
                }
                return [
                    'instituicao' => $linha->instituicao, 'usuario' => $linha->usuario, 'perfil' => $linha->perfil,
                    'primeiro' => Carbon::parse($linha->primeiro)->format('d/m/Y H:i:s'),
                ];
            })->all()];
    }

    private function vinculosAtuaisQuery(int $regiaoId, ?int $instituicaoId = null)
    {
        return DB::table('perfil_user as v')
            ->join('perfils as vp', 'vp.id', '=', 'v.perfil_id')
            ->join('instituicoes_instituicoes as vi', 'vi.id', '=', 'v.instituicao_id')
            ->whereNull('vp.deleted_at')->whereNull('vi.deleted_at')->where('vi.ativo', 1)
            ->whereIn('vi.id', $this->instituicoesDaRegiao($regiaoId))
            ->when($instituicaoId, fn ($q) => $q->where('v.instituicao_id', $instituicaoId));
    }

    private function termoLike(string $termo): string
    {
        return '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], trim($termo)).'%';
    }

    public function instituicoes(int $regiaoId)
    {
        return DB::table('instituicoes_instituicoes as i')
            ->join('instituicoes_tiposinstituicao as t', 't.id', '=', 'i.tipo_instituicao_id')
            ->whereIn('i.id', $this->instituicoesDaRegiao($regiaoId))
            ->whereNull('i.deleted_at')
            ->select('i.id', 'i.nome', 't.nome as tipo')
            ->orderBy('t.nome')->orderBy('i.nome')->get();
    }

    public function instituicaoPermitida(int $regiaoId, int $instituicaoId): bool
    {
        return DB::table('instituicoes_instituicoes')->where('id', $instituicaoId)
            ->whereNull('deleted_at')->whereIn('id', $this->instituicoesDaRegiao($regiaoId))->exists();
    }

    private function instituicoesDaRegiao(int $regiaoId)
    {
        return DB::table('instituicoes_instituicoes as regional')
            ->leftJoin('instituicoes_instituicoes as pai_regional', 'pai_regional.id', '=', 'regional.instituicao_pai_id')
            ->leftJoin('instituicoes_instituicoes as avo_regional', 'avo_regional.id', '=', 'pai_regional.instituicao_pai_id')
            ->select('regional.id')->where(function ($q) use ($regiaoId) {
                $q->where('regional.id', $regiaoId)->orWhere('regional.instituicao_pai_id', $regiaoId)
                    ->orWhere('pai_regional.instituicao_pai_id', $regiaoId)
                    ->orWhere('avo_regional.instituicao_pai_id', $regiaoId);
            });
    }

}
