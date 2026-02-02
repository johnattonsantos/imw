<?php

namespace App\Http\Controllers;

use App\Exports\AuditoriasExport;
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

        [$users, $events, $entidades] = $this->filterOptions();

        return view('auditorias.index', compact('audits', 'users', 'events', 'entidades'));
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

        return [$users, $events, $entidades];
    }


}
