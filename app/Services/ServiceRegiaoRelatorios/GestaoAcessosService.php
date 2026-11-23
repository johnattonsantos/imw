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
        $sessoes = DB::table('gestao_acessos_sessao_perfis as p')
            ->join('gestao_acessos_sessoes as s', 's.id', '=', 'p.sessao_id')
            ->where('p.regiao_id', $regiaoId)
            ->whereNotNull('s.login_em')
            ->where('s.login_em', '>=', $inicio.' 00:00:00')->where('s.login_em', '<', $limite)
            ->when($instituicaoId, fn ($q) => $q->where('p.instituicao_id', $instituicaoId))
            ->select('p.instituicao_nome as instituicao', 'p.usuario_nome as usuario', 'p.perfil_nome as perfil',
                's.id as sessao_id', 's.login_em as primeiro', 's.logout_em as ultimo', 'p.primeiro_uso', 'p.ultimo_uso')
            ->selectRaw("'sessao' as origem, p.id as ordenacao_id");

        $instituicoes = $this->instituicoesDaRegiao($regiaoId);
        $logins = DB::table(config('audit.drivers.database.table', 'audits').' as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('instituicoes_instituicoes as i', 'i.id', '=', 'a.instituicao_id')
            ->where('a.event', 'login')->where('a.user_type', User::class)
            ->whereNotNull('a.created_at')
            ->where('a.created_at', '>=', $inicio.' 00:00:00')->where('a.created_at', '<', $limite)
            ->when($instituicaoId, fn ($q) => $q->where('a.instituicao_id', $instituicaoId))
            ->where(function ($q) use ($instituicoes) {
                $q->whereIn('a.instituicao_id', clone $instituicoes)
                    ->orWhere(function ($q) use ($instituicoes) {
                        // Never infer the institution of a historical login from ambiguous current links.
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
            ->whereNotExists(function ($q) {
                $q->selectRaw('1')->from('gestao_acessos_sessoes as registrada')
                    ->whereColumn('registrada.user_id', 'a.user_id')
                    ->whereRaw("registrada.id = JSON_UNQUOTE(JSON_EXTRACT(CASE WHEN JSON_VALID(a.new_values) THEN a.new_values ELSE '{}' END, '$.gestao_sessao_id'))");
            })
            ->selectRaw("COALESCE(i.nome, 'Não informado no histórico') as instituicao")
            ->addSelect('u.name as usuario')
            ->selectRaw("'Não informado no histórico' as perfil, NULL as sessao_id")
            ->addSelect('a.created_at as primeiro')->selectRaw('NULL as ultimo')
            ->addSelect('a.created_at as primeiro_uso', 'a.created_at as ultimo_uso')
            ->selectRaw("'login_antigo' as origem, a.id as ordenacao_id");

        if (trim($usuario) !== '') {
            $nome = $this->termoLike($usuario);
            $sessoes->whereRaw("p.usuario_nome LIKE ? ESCAPE '!'", [$nome]);
            $logins->whereRaw("u.name LIKE ? ESCAPE '!'", [$nome]);
        }

        return DB::query()->fromSub($sessoes->unionAll($logins), 'acessos');
    }

    public function sessoes(int $regiaoId, string $inicio, string $fim, string $usuario = '')
    {
        return $this->consultaSessoes($regiaoId, $inicio, $fim, $usuario)
            ->orderByDesc('primeiro')->orderByDesc('origem')->orderByDesc('ordenacao_id')
            ->paginate(50)->withQueryString();
    }

    public function datatable(int $regiaoId, array $filtros, array $tabela): array
    {
        $query = $this->consultaSessoes($regiaoId, $filtros['data_inicial'], $filtros['data_final'],
            trim($filtros['usuario'] ?? ''), isset($filtros['instituicao_id']) ? (int) $filtros['instituicao_id'] : null);
        $total = (clone $query)->count();
        $busca = trim(data_get($tabela, 'search.value', '') ?? '');
        if ($busca !== '') {
            $query->where(function ($q) use ($busca) {
                foreach (['instituicao', 'usuario', 'perfil'] as $coluna) {
                    $q->orWhereRaw("acessos.$coluna LIKE ? ESCAPE '!'", [$this->termoLike($busca)]);
                }
                $q->orWhereRaw("DATE_FORMAT(acessos.primeiro, '%d/%m/%Y %H:%i:%s') LIKE ? ESCAPE '!'", [$this->termoLike($busca)]);
            });
        }
        $filtrado = $busca === '' ? $total : (clone $query)->count();
        $colunas = ['instituicao', 'usuario', 'perfil', 'primeiro'];
        $coluna = $colunas[(int) data_get($tabela, 'order.0.column', 3)] ?? 'primeiro';
        $direcao = data_get($tabela, 'order.0.dir') === 'asc' ? 'asc' : 'desc';
        $linhas = $query->orderBy($coluna, $direcao)->orderByDesc('origem')->orderByDesc('ordenacao_id')
            ->offset(max(0, (int) ($tabela['start'] ?? 0)))
            ->limit(min(100, max(10, (int) ($tabela['length'] ?? 25))))->get();

        return ['draw' => (int) ($tabela['draw'] ?? 0), 'recordsTotal' => $total, 'recordsFiltered' => $filtrado,
            'data' => $linhas->map(fn ($linha) => [
                'instituicao' => $linha->instituicao, 'usuario' => $linha->usuario, 'perfil' => $linha->perfil,
                'primeiro' => Carbon::parse($linha->primeiro)->format('d/m/Y H:i:s'),
            ])->all()];
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

    public function quadro(int $regiaoId, int $ano, string $inicio, string $fim, ?string $grupo = null)
    {
        $contas = DB::table('perfil_user as pu')->join('users as u', 'u.id', '=', 'pu.user_id')
            ->join('perfils as p', 'p.id', '=', 'pu.perfil_id')
            ->whereNull('u.deleted_at')->whereNull('p.deleted_at')
            ->select('pu.user_id', 'pu.perfil_id', 'pu.instituicao_id', 'u.name as usuario', 'p.nome as perfil')
            ->distinct();
        if ($grupo === 'pastores') {
            $contas->where('p.nivel', 'I')->where('p.nome', 'Pastor');
        } elseif ($grupo === 'sds') {
            $contas->where('p.nivel', 'D')->whereIn('p.nome', ['Administrador Distrito', 'Administrador - Distrito', 'Superintendente Distrital', 'SD']);
        }

        $acessos = DB::table('gestao_acessos')->where('regiao_id', $regiaoId)
            ->whereBetween('data_acesso', [sprintf('%04d-01-01', $ano), sprintf('%04d-12-31', $ano)])
            ->select('user_id', 'perfil_id', 'instituicao_id')
            ->selectRaw('COUNT(CASE WHEN data_acesso BETWEEN ? AND ? THEN id END) as dias_periodo', [$inicio, $fim])
            ->selectRaw('MIN(CASE WHEN data_acesso BETWEEN ? AND ? THEN primeiro_acesso END) as primeiro', [$inicio, $fim])
            ->selectRaw('MAX(CASE WHEN data_acesso BETWEEN ? AND ? THEN ultimo_acesso END) as ultimo', [$inicio, $fim]);
        for ($mes = 1; $mes <= 12; $mes++) {
            $data = Carbon::create($ano, $mes, 1);
            $acessos->selectRaw('COUNT(CASE WHEN data_acesso BETWEEN ? AND ? THEN id END) as mes_'.$mes,
                [$data->toDateString(), $data->endOfMonth()->toDateString()]);
        }
        $acessos->groupBy('user_id', 'perfil_id', 'instituicao_id');

        $query = DB::table('instituicoes_instituicoes as i')
            ->leftJoin('instituicoes_instituicoes as pai', 'pai.id', '=', 'i.instituicao_pai_id')
            ->leftJoinSub($contas, 'conta', 'conta.instituicao_id', '=', 'i.id')
            ->leftJoinSub($acessos, 'a', function ($join) {
                $join->on('a.user_id', '=', 'conta.user_id')->on('a.perfil_id', '=', 'conta.perfil_id')
                    ->on('a.instituicao_id', '=', 'i.id');
            })
            ->whereNull('i.deleted_at')->where('i.ativo', 1)
            ->where(function ($q) use ($regiaoId) {
                $q->where(fn ($q) => $q->where('i.tipo_instituicao_id', 3)->where('i.id', $regiaoId))
                    ->orWhere(fn ($q) => $q->where('i.tipo_instituicao_id', 2)->where('i.instituicao_pai_id', $regiaoId))
                    ->orWhere(fn ($q) => $q->where('i.tipo_instituicao_id', 1)->where('pai.tipo_instituicao_id', 2)
                        ->where('pai.instituicao_pai_id', $regiaoId)->whereNull('pai.deleted_at'));
            })
            ->select('i.nome as instituicao', 'i.tipo_instituicao_id', 'conta.user_id', 'conta.usuario', 'conta.perfil', 'a.primeiro', 'a.ultimo')
            ->selectRaw('COALESCE(a.dias_periodo, 0) as dias_periodo');
        for ($mes = 1; $mes <= 12; $mes++) {
            $query->selectRaw('COALESCE(a.mes_'.$mes.', 0) as mes_'.$mes);
        }
        if ($grupo) {
            $query->where('i.tipo_instituicao_id', $grupo === 'pastores' ? 1 : 2);
        } else {
            $query->whereNotNull('conta.user_id');
        }

        return $query->orderBy('i.nome')->orderBy('conta.usuario')->get();
    }

    public static function situacaoMes(int $ano, int $mes, int $dias, ?string $inicioMonitoramento, bool $temUsuario = true): string
    {
        if (!$temUsuario) {
            return 'Sem usuário/perfil cadastrado';
        }
        $inicio = Carbon::create($ano, $mes, 1)->startOfDay();
        $fim = $inicio->copy()->endOfMonth();
        if ($inicio->isFuture()) {
            return 'Período futuro';
        }
        if (!$inicioMonitoramento || $fim->lt(Carbon::parse($inicioMonitoramento))) {
            return 'Sem histórico';
        }
        if ($dias > 0) {
            return 'Acessou ('.$dias.' dia(s))';
        }
        if ($inicio->lt(Carbon::parse($inicioMonitoramento))) {
            return 'Sem registro (cobertura parcial)';
        }
        return $fim->isFuture() ? 'Sem acesso até agora' : 'Não acessou';
    }
}
