<?php 

namespace App\DataTables;

use App\Models\RolMembroRecadastramento;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class RolMembroRecadastramentoDatatable extends AbstractDatatable
{
    use Identifiable;

    public function getQueryBuilder($parameters): Builder
    {
        $search = trim($parameters['search'] ?? '');

        return RolMembroRecadastramento::with('notificacaoTransferenciaAtiva.igrejaDestino')
            ->addSelect([
                'validado_migracao' => DB::table('membresia_migracao')
                    ->select('validado')
                    ->whereColumn('membresia_migracao.id', 'vw_rol_membros_recadastro.membro_id')
                    ->limit(1),
            ])
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where('membro', 'like', "%{$search}%");
            })
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('membresia_migracao')
                    ->whereColumn('membresia_migracao.id', 'vw_rol_membros_recadastro.membro_id')
                    ->where('membresia_migracao.validado', 0);
            })
            ->when((isset($parameters['status']) && $parameters['status'] == 'rol_atual' || !isset($parameters['status'])), function ($query) {
                $query->where('status', 'A');
            })
            ->when(isset($parameters['status']) && $parameters['status'] == 'inativo', function ($query) {
                $query->where('status', 'I');
            })
            ->when(isset($parameters['status']) && $parameters['status'] == 'rol_permanente', function ($query) {
                $query->whereIn('status', ['A', 'I']);
            })
            ->when(isset($parameters['status']) && $parameters['status'] == 'has_errors', function ($query) {
                $query->where('has_errors', 1);
            });
    }

    public function dataTable(Builder $queryBuilder, array $requestData): JsonResponse
    {
        return DataTables::of($queryBuilder)
            ->order(function ($query) use ($requestData) {
                [ $order ] = $requestData['order'];

                $query->when($order['column'] == 0, fn ($q) => $q->orderBy('numero_rol', $order['dir']))
                      ->when($order['column'] == 1, fn ($q) => $q->orderBy('membro', $order['dir']))
                      ->when($order['column'] == 2, fn ($q) => $q->orderBy('dt_recepcao', $order['dir']))
                      ->when($order['column'] == 3, fn ($q) => $q->orderBy('dt_exclusao', $order['dir']))
                      ->when($order['column'] == 4, fn ($q) => $q->orderBy('congregacao', $order['dir']));
            })
            ->addColumn('recepcao', function (RolMembroRecadastramento $rolMembro) {
                return $rolMembro->dt_recepcao ? $rolMembro->dt_recepcao->format('d/m/Y') : ''; 
            })
            ->addColumn('exclusao', function (RolMembroRecadastramento $rolMembro) {
                return $rolMembro->dt_exclusao ? $rolMembro->dt_exclusao->format('d/m/Y') : '';
            })
            ->addColumn('validado', function (RolMembroRecadastramento $rolMembro) {
                return (int) ($rolMembro->validado_migracao ?? 0) === 1 ? 'Sim' : 'Não';
            })
            ->addColumn('actions', function (RolMembroRecadastramento $rolMembro) {
                return view('membros.slice-actions-recadastramento', ['rolMembro' => $rolMembro]);
            })
            ->addColumn('igreja_atual', function (RolMembroRecadastramento $rolMembro) {
                return optional($rolMembro->igrejaAtual)->nome;
            })
            ->make(true);
    }
}
