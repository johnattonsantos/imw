<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ModuloGeralExport;
use App\Http\Controllers\Controller;
use App\Models\Perfil;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ModuloGeralController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->isAdminSistema()) {
            return redirect()->route('admin.index')->with('error', 'Apenas Administrador do Sistema pode acessar o Módulo Geral.');
        }

        return view('admin.modulo-geral', $this->buildDashboardData($request));
    }

    public function exportXlsx(Request $request)
    {
        if (!$this->isAdminSistema()) {
            return redirect()->route('admin.index')->with('error', 'Apenas Administrador do Sistema pode exportar o Módulo Geral.');
        }

        $data = $this->buildDashboardData($request);

        return Excel::download(
            new ModuloGeralExport($data),
            'modulo_geral_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        if (!$this->isAdminSistema()) {
            return redirect()->route('admin.index')->with('error', 'Apenas Administrador do Sistema pode exportar o Módulo Geral.');
        }

        $data = $this->buildDashboardData($request);
        $data['geradoEm'] = now();

        $pdf = FacadePdf::loadView('admin.modulo-geral-pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download('modulo_geral_' . now()->format('Ymd_His') . '.pdf');
    }

    private function buildDashboardData(Request $request): array
    {
        $regioes = DB::table('instituicoes_instituicoes')
            ->select('id', 'nome')
            ->where('ativo', 1)
            ->where(function ($query) {
                $query->where('tipo_instituicao_id', 5)
                    ->orWhereNull('instituicao_pai_id');
            })
            ->orderBy('nome')
            ->get();

        $regiaoId = (int) $request->input('regiao_id');
        $regiaoId = $regiaoId > 0 ? $regiaoId : null;

        $distritos = collect();
        if ($regiaoId) {
            $distritos = DB::table('instituicoes_instituicoes')
                ->select('id', 'nome')
                ->where('ativo', 1)
                ->where('tipo_instituicao_id', 2)
                ->where(function ($query) use ($regiaoId) {
                    $query->where('regiao_id', $regiaoId)
                        ->orWhere('instituicao_pai_id', $regiaoId);
                })
                ->orderBy('nome')
                ->get();
        }

        $distritoId = (int) $request->input('distrito_id');
        if ($distritoId <= 0 || !$distritos->contains('id', $distritoId)) {
            $distritoId = null;
        }

        $igrejas = collect();
        if ($distritoId) {
            $igrejas = DB::table('instituicoes_instituicoes')
                ->select('id', 'nome')
                ->where('ativo', 1)
                ->where('tipo_instituicao_id', 1)
                ->where('instituicao_pai_id', $distritoId)
                ->orderBy('nome')
                ->get();
        } elseif ($regiaoId) {
            $igrejas = DB::table('instituicoes_instituicoes')
                ->select('id', 'nome')
                ->where('ativo', 1)
                ->where('tipo_instituicao_id', 1)
                ->where('regiao_id', $regiaoId)
                ->orderBy('nome')
                ->get();
        }

        $igrejaId = (int) $request->input('igreja_id');
        if ($igrejaId <= 0 || !$igrejas->contains('id', $igrejaId)) {
            $igrejaId = null;
        }

        $periodoInicioInput = $request->input('periodo_inicio');
        $periodoFimInput = $request->input('periodo_fim');
        $periodoInicio = $this->parseDateOrDefault($periodoInicioInput, Carbon::now()->subDays(30)->startOfDay())->startOfDay();
        $periodoFim = $this->parseDateOrDefault($periodoFimInput, Carbon::now()->endOfDay())->endOfDay();
        $periodoResumo = $periodoInicio->format('d/m/Y') . ' a ' . $periodoFim->format('d/m/Y');

        $usuariosBase = DB::table('users as u')
            ->whereNull('u.deleted_at');
        $this->applyUserScope($usuariosBase, $regiaoId, $distritoId, $igrejaId);

        $instituicoesBase = DB::table('instituicoes_instituicoes as i')
            ->where('i.ativo', 1);
        $this->applyInstitutionScope($instituicoesBase, $regiaoId, $distritoId, $igrejaId);

        $clerigosBase = DB::table('pessoas_pessoas as p')
            ->whereNull('p.deleted_at');
        $this->applyClerigoScope($clerigosBase, $regiaoId, $distritoId, $igrejaId);

        $nomeacoesBase = DB::table('pessoas_nomeacoes as n')
            ->join('pessoas_pessoas as p', 'p.id', '=', 'n.pessoa_id')
            ->leftJoin('instituicoes_instituicoes as i', 'i.id', '=', 'n.instituicao_id')
            ->whereNull('n.data_termino')
            ->whereNull('n.deleted_at')
            ->whereNull('p.deleted_at');
        $this->applyNomeacaoScope($nomeacoesBase, $regiaoId, $distritoId, $igrejaId);

        $totalUsuarios = (clone $usuariosBase)->distinct()->count('u.id');
        $totalInstituicoes = (clone $instituicoesBase)->count('i.id');
        $totalClerigos = (clone $clerigosBase)->count('p.id');
        $totalNomeacoesAtivas = (clone $nomeacoesBase)->count('n.id');
        $totalUsuariosSemRegiao = (clone $usuariosBase)->whereNull('u.regiao_id')->distinct()->count('u.id');

        $usuariosPorRegiao = (clone $usuariosBase)
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'u.regiao_id')
            ->selectRaw("COALESCE(regiao.nome, 'Sem região') as regiao_nome, COUNT(DISTINCT u.id) as total")
            ->groupBy('regiao_nome')
            ->orderBy('regiao_nome')
            ->get();

        $instituicoesPorRegiao = (clone $instituicoesBase)
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', DB::raw('COALESCE(i.regiao_id, i.id)'))
            ->selectRaw("COALESCE(regiao.nome, 'Sem região') as regiao_nome, COUNT(i.id) as total")
            ->groupBy('regiao_nome')
            ->orderBy('regiao_nome')
            ->get();

        $clerigosPorRegiao = (clone $clerigosBase)
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'p.regiao_id')
            ->selectRaw("COALESCE(regiao.nome, 'Sem região') as regiao_nome, COUNT(p.id) as total")
            ->groupBy('regiao_nome')
            ->orderBy('regiao_nome')
            ->get();

        $nomeacoesAtivasPorRegiao = (clone $nomeacoesBase)
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', DB::raw('COALESCE(i.regiao_id, p.regiao_id)'))
            ->selectRaw("COALESCE(regiao.nome, 'Sem região') as regiao_nome, COUNT(n.id) as total")
            ->groupBy('regiao_nome')
            ->orderBy('regiao_nome')
            ->get();

        $nomeacoesPorInstituicao = (clone $nomeacoesBase)
            ->selectRaw("COALESCE(i.nome, 'Sem instituição') as instituicao_nome, COUNT(n.id) as total")
            ->groupBy('instituicao_nome')
            ->orderByDesc('total')
            ->limit(25)
            ->get();

        $adminAliasesSql = $this->sqlInList(Perfil::aliasesPorCodigo(Perfil::CODIGO_ADMINISTRADOR_SISTEMA));
        $crieAliasesSql = $this->sqlInList(Perfil::aliasesPorCodigo(Perfil::CODIGO_CRIE));

        $usuariosPerfisBase = DB::table('users as u')
            ->leftJoin('perfil_user as pu', 'pu.user_id', '=', 'u.id')
            ->leftJoin('perfils as pr', function ($join) {
                $join->on('pr.id', '=', 'pu.perfil_id')
                    ->whereNull('pr.deleted_at');
            })
            ->whereNull('u.deleted_at');
        $this->applyUserScope($usuariosPerfisBase, $regiaoId, $distritoId, $igrejaId);

        $totalUsuariosAdminSistema = (clone $usuariosPerfisBase)
            ->whereRaw("LOWER(pr.nome) IN ($adminAliasesSql)")
            ->distinct()
            ->count('u.id');

        $totalUsuariosCrie = (clone $usuariosPerfisBase)
            ->whereRaw("LOWER(pr.nome) IN ($crieAliasesSql)")
            ->distinct()
            ->count('u.id');

        $perfisEstrategicosPorRegiao = (clone $usuariosPerfisBase)
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'u.regiao_id')
            ->selectRaw("
                COALESCE(regiao.nome, 'Sem região') as regiao_nome,
                COUNT(DISTINCT CASE WHEN LOWER(pr.nome) IN ($adminAliasesSql) THEN u.id END) as total_admin_sistema,
                COUNT(DISTINCT CASE WHEN LOWER(pr.nome) IN ($crieAliasesSql) THEN u.id END) as total_crie
            ")
            ->groupBy('regiao_nome')
            ->orderBy('regiao_nome')
            ->get();

        $auditsBase = DB::table('audits as a')
            ->leftJoin('users as audit_user', 'audit_user.id', '=', 'a.user_id')
            ->leftJoin('instituicoes_instituicoes as ai', 'ai.id', '=', 'a.instituicao_id')
            ->whereBetween('a.created_at', [$periodoInicio->toDateTimeString(), $periodoFim->toDateTimeString()]);
        $this->applyAuditInstitutionScope($auditsBase, $regiaoId, $distritoId, $igrejaId);

        $totalAuditoriasPeriodo = (clone $auditsBase)->count('a.id');
        $totalAuditoriasHoje = (clone $auditsBase)->whereDate('a.created_at', Carbon::today())->count('a.id');
        $totalAuditoriasLoginFalho = (clone $auditsBase)->whereIn('a.event', ['login_failed', 'LOGIN_FAILED'])->count('a.id');

        $auditoriasPorRegiao = (clone $auditsBase)
            ->leftJoin('instituicoes_instituicoes as regiao_audit', 'regiao_audit.id', '=', DB::raw('COALESCE(ai.regiao_id, ai.id)'))
            ->selectRaw("COALESCE(regiao_audit.nome, 'Sem região') as regiao_nome, COUNT(a.id) as total")
            ->groupBy('regiao_nome')
            ->orderByDesc('total')
            ->get();

        $auditoriasPorEvento = (clone $auditsBase)
            ->selectRaw("UPPER(COALESCE(a.event, 'SEM_EVENTO')) as evento, COUNT(a.id) as total")
            ->groupBy('evento')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $auditoriasPorUsuario = (clone $auditsBase)
            ->selectRaw("COALESCE(audit_user.name, 'Sistema') as usuario_nome, COUNT(a.id) as total")
            ->groupBy('usuario_nome')
            ->orderByDesc('total')
            ->limit(15)
            ->get();

        $auditoriasRecentes = (clone $auditsBase)
            ->leftJoin('instituicoes_instituicoes as regiao_audit', 'regiao_audit.id', '=', DB::raw('COALESCE(ai.regiao_id, ai.id)'))
            ->select(
                'a.id',
                'a.created_at',
                'a.event',
                'a.auditable_type',
                'a.auditable_id',
                'a.ip_address',
                DB::raw("COALESCE(audit_user.name, 'Sistema') as usuario_nome"),
                DB::raw("COALESCE(ai.nome, 'Sem instituição') as instituicao_nome"),
                DB::raw("COALESCE(regiao_audit.nome, 'Sem região') as regiao_nome")
            )
            ->orderByDesc('a.created_at')
            ->limit(30)
            ->get();

        $filtros = [
            'regiao_id' => $regiaoId,
            'distrito_id' => $distritoId,
            'igreja_id' => $igrejaId,
            'periodo_inicio' => $periodoInicio->format('Y-m-d'),
            'periodo_fim' => $periodoFim->format('Y-m-d'),
        ];

        return [
            'regioes' => $regioes,
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'filtros' => $filtros,
            'periodoResumo' => $periodoResumo,
            'totalUsuarios' => $totalUsuarios,
            'totalInstituicoes' => $totalInstituicoes,
            'totalClerigos' => $totalClerigos,
            'totalNomeacoesAtivas' => $totalNomeacoesAtivas,
            'totalUsuariosSemRegiao' => $totalUsuariosSemRegiao,
            'totalUsuariosAdminSistema' => $totalUsuariosAdminSistema,
            'totalUsuariosCrie' => $totalUsuariosCrie,
            'totalAuditoriasPeriodo' => $totalAuditoriasPeriodo,
            'totalAuditoriasHoje' => $totalAuditoriasHoje,
            'totalAuditoriasLoginFalho' => $totalAuditoriasLoginFalho,
            'usuariosPorRegiao' => $usuariosPorRegiao,
            'instituicoesPorRegiao' => $instituicoesPorRegiao,
            'clerigosPorRegiao' => $clerigosPorRegiao,
            'nomeacoesAtivasPorRegiao' => $nomeacoesAtivasPorRegiao,
            'nomeacoesPorInstituicao' => $nomeacoesPorInstituicao,
            'perfisEstrategicosPorRegiao' => $perfisEstrategicosPorRegiao,
            'auditoriasPorRegiao' => $auditoriasPorRegiao,
            'auditoriasPorEvento' => $auditoriasPorEvento,
            'auditoriasPorUsuario' => $auditoriasPorUsuario,
            'auditoriasRecentes' => $auditoriasRecentes,
        ];
    }

    private function isAdminSistema(): bool
    {
        $perfilNome = (string) optional(session('session_perfil'))->perfil_nome;
        return Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
    }

    private function applyInstitutionScope($query, ?int $regiaoId, ?int $distritoId, ?int $igrejaId): void
    {
        if ($igrejaId) {
            $query->where('i.id', $igrejaId);
            return;
        }

        if ($distritoId) {
            $query->where(function ($subquery) use ($distritoId) {
                $subquery->where('i.id', $distritoId)
                    ->orWhere('i.instituicao_pai_id', $distritoId);
            });
            return;
        }

        if ($regiaoId) {
            $query->where(function ($subquery) use ($regiaoId) {
                $subquery->where('i.id', $regiaoId)
                    ->orWhere('i.regiao_id', $regiaoId);
            });
        }
    }

    private function applyUserScope($query, ?int $regiaoId, ?int $distritoId, ?int $igrejaId): void
    {
        if ($igrejaId) {
            $query->whereExists(function ($subquery) use ($igrejaId) {
                $subquery->select(DB::raw(1))
                    ->from('perfil_user as pu')
                    ->whereColumn('pu.user_id', 'u.id')
                    ->where('pu.instituicao_id', $igrejaId);
            });
            return;
        }

        if ($distritoId) {
            $query->whereExists(function ($subquery) use ($distritoId) {
                $subquery->select(DB::raw(1))
                    ->from('perfil_user as pu')
                    ->join('instituicoes_instituicoes as iu', 'iu.id', '=', 'pu.instituicao_id')
                    ->whereColumn('pu.user_id', 'u.id')
                    ->where(function ($nested) use ($distritoId) {
                        $nested->where('iu.id', $distritoId)
                            ->orWhere('iu.instituicao_pai_id', $distritoId);
                    });
            });
            return;
        }

        if ($regiaoId) {
            $query->where(function ($subquery) use ($regiaoId) {
                $subquery->where('u.regiao_id', $regiaoId)
                    ->orWhereExists(function ($exists) use ($regiaoId) {
                        $exists->select(DB::raw(1))
                            ->from('perfil_user as pu')
                            ->join('instituicoes_instituicoes as iu', 'iu.id', '=', 'pu.instituicao_id')
                            ->whereColumn('pu.user_id', 'u.id')
                            ->where(function ($nested) use ($regiaoId) {
                                $nested->where('iu.id', $regiaoId)
                                    ->orWhere('iu.regiao_id', $regiaoId);
                            });
                    });
            });
        }
    }

    private function applyClerigoScope($query, ?int $regiaoId, ?int $distritoId, ?int $igrejaId): void
    {
        if ($igrejaId) {
            $query->whereExists(function ($subquery) use ($igrejaId) {
                $subquery->select(DB::raw(1))
                    ->from('pessoas_nomeacoes as n')
                    ->whereColumn('n.pessoa_id', 'p.id')
                    ->whereNull('n.data_termino')
                    ->whereNull('n.deleted_at')
                    ->where('n.instituicao_id', $igrejaId);
            });
            return;
        }

        if ($distritoId) {
            $query->whereExists(function ($subquery) use ($distritoId) {
                $subquery->select(DB::raw(1))
                    ->from('pessoas_nomeacoes as n')
                    ->join('instituicoes_instituicoes as i', 'i.id', '=', 'n.instituicao_id')
                    ->whereColumn('n.pessoa_id', 'p.id')
                    ->whereNull('n.data_termino')
                    ->whereNull('n.deleted_at')
                    ->where(function ($nested) use ($distritoId) {
                        $nested->where('i.id', $distritoId)
                            ->orWhere('i.instituicao_pai_id', $distritoId);
                    });
            });
            return;
        }

        if ($regiaoId) {
            $query->where('p.regiao_id', $regiaoId);
        }
    }

    private function applyNomeacaoScope($query, ?int $regiaoId, ?int $distritoId, ?int $igrejaId): void
    {
        if ($igrejaId) {
            $query->where('n.instituicao_id', $igrejaId);
            return;
        }

        if ($distritoId) {
            $query->where(function ($subquery) use ($distritoId) {
                $subquery->where('i.id', $distritoId)
                    ->orWhere('i.instituicao_pai_id', $distritoId);
            });
            return;
        }

        if ($regiaoId) {
            $query->where(function ($subquery) use ($regiaoId) {
                $subquery->where('i.id', $regiaoId)
                    ->orWhere('i.regiao_id', $regiaoId)
                    ->orWhere('p.regiao_id', $regiaoId);
            });
        }
    }

    private function applyAuditInstitutionScope($query, ?int $regiaoId, ?int $distritoId, ?int $igrejaId): void
    {
        if ($igrejaId) {
            $query->where('a.instituicao_id', $igrejaId);
            return;
        }

        if ($distritoId) {
            $query->whereExists(function ($subquery) use ($distritoId) {
                $subquery->select(DB::raw(1))
                    ->from('instituicoes_instituicoes as i_scope')
                    ->whereColumn('i_scope.id', 'a.instituicao_id')
                    ->where(function ($nested) use ($distritoId) {
                        $nested->where('i_scope.id', $distritoId)
                            ->orWhere('i_scope.instituicao_pai_id', $distritoId);
                    });
            });
            return;
        }

        if ($regiaoId) {
            $query->whereExists(function ($subquery) use ($regiaoId) {
                $subquery->select(DB::raw(1))
                    ->from('instituicoes_instituicoes as i_scope')
                    ->whereColumn('i_scope.id', 'a.instituicao_id')
                    ->where(function ($nested) use ($regiaoId) {
                        $nested->where('i_scope.id', $regiaoId)
                            ->orWhere('i_scope.regiao_id', $regiaoId)
                            ->orWhere('i_scope.instituicao_pai_id', $regiaoId);
                    });
            });
        }
    }

    private function sqlInList(array $values): string
    {
        $normalized = array_map(function ($value) {
            $value = mb_strtolower(trim((string) $value), 'UTF-8');
            $value = str_replace("'", "''", $value);
            return "'{$value}'";
        }, $values);

        return implode(', ', array_unique($normalized));
    }

    private function parseDateOrDefault(?string $date, Carbon $default): Carbon
    {
        try {
            return $date ? Carbon::parse($date) : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
