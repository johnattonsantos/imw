<?php

namespace App\Http\Controllers;

use App\Exports\AuditoriasExport;
use App\Models\InstituicoesInstituicao;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $validated = $this->validateFilters($request);

        $audits = $this->buildFilteredQuery($validated)
            ->with('user')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        [$users, $events, $entidades, $instituicoes] = $this->filterOptions();
        $instituicaoIds = $audits->getCollection()
            ->map(fn (Audit $audit) => $this->resolveAuditInstituicaoId($audit))
            ->filter()
            ->unique()
            ->values();

        $instituicaoMap = InstituicoesInstituicao::query()
            ->whereIn('id', $instituicaoIds)
            ->pluck('nome', 'id');

        return view('auditorias.index', compact('audits', 'users', 'events', 'entidades', 'instituicoes', 'instituicaoMap'));
    }

    public function exportXlsx(Request $request)
    {
        $validated = $this->validateFilters($request);

        $audits = $this->buildFilteredQuery($validated)
            ->with('user')
            ->latest('created_at')
            ->get();

        return Excel::download(
            new AuditoriasExport($audits),
            'auditorias_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $validated = $this->validateFilters($request);

        $audits = $this->buildFilteredQuery($validated)
            ->with('user')
            ->latest('created_at')
            ->get();

        $pdf = FacadePdf::loadView('auditorias.pdf', [
            'audits' => $audits,
            'filtros' => $validated,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('auditorias_' . now()->format('Ymd_His') . '.pdf');
    }

    private function validateFilters(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'instituicao_id' => ['nullable', 'integer', 'exists:instituicoes_instituicoes,id'],
            'event' => ['nullable', 'string', 'max:50'],
            'auditable_type' => ['nullable', 'string', 'max:255'],
            'periodo_inicio' => ['nullable', 'date'],
            'periodo_fim' => ['nullable', 'date', 'after_or_equal:periodo_inicio'],
        ]);
    }

    private function buildFilteredQuery(array $filters): Builder
    {
        $query = Audit::query();

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['instituicao_id'])) {
            $instituicaoId = (int) $filters['instituicao_id'];
            $query->where(function (Builder $builder) use ($instituicaoId) {
                $builder->where('instituicao_id', $instituicaoId)
                    ->orWhere('new_values', 'like', '%"instituicao_id":' . $instituicaoId . '%')
                    ->orWhere('new_values', 'like', '%"instituicao_id":"' . $instituicaoId . '"%')
                    ->orWhere('old_values', 'like', '%"instituicao_id":' . $instituicaoId . '%')
                    ->orWhere('old_values', 'like', '%"instituicao_id":"' . $instituicaoId . '"%');
            });
        }

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (!empty($filters['periodo_inicio'])) {
            $query->whereDate('created_at', '>=', $filters['periodo_inicio']);
        }

        if (!empty($filters['periodo_fim'])) {
            $query->whereDate('created_at', '<=', $filters['periodo_fim']);
        }

        return $query;
    }

    private function filterOptions(): array
    {
        $users = User::query()
            ->whereIn('id', Audit::query()->whereNotNull('user_id')->select('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $events = Audit::query()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        $entidades = Audit::query()
            ->select('auditable_type')
            ->whereNotNull('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type');

        $instituicoes = InstituicoesInstituicao::query()
            ->whereIn('id', Audit::query()->whereNotNull('instituicao_id')->select('instituicao_id')->distinct())
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return [$users, $events, $entidades, $instituicoes];
    }

    private function resolveAuditInstituicaoId(Audit $audit): ?int
    {
        if (!empty($audit->instituicao_id)) {
            return (int) $audit->instituicao_id;
        }

        $newValues = is_array($audit->new_values) ? $audit->new_values : (json_decode($audit->new_values ?? '', true) ?: []);
        $oldValues = is_array($audit->old_values) ? $audit->old_values : (json_decode($audit->old_values ?? '', true) ?: []);
        $instituicaoId = data_get($newValues, 'instituicao_id', data_get($oldValues, 'instituicao_id'));

        return !empty($instituicaoId) ? (int) $instituicaoId : null;
    }


}
