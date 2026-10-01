<?php 

namespace App\DataTables;

use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CongregadosDatatable extends AbstractDatatable
{
    use Identifiable;

    protected function getQueryBuilder($parameters): Builder
    {
        return MembresiaMembro::with('congregacao')
            ->select('membresia_membros.*', 'congregacoes_congregacoes.nome as congregacao')
            ->leftJoin('congregacoes_congregacoes', 'congregacoes_congregacoes.id', '=', 'membresia_membros.congregacao_id')
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->where('vinculo', MembresiaMembro::VINCULO_CONGREGADO)
            ->when($parameters['search'], function ($query) use ($parameters) {
                $query->where('membresia_membros.nome', 'like', "%{$parameters['search']}%");
            })
            ->when($parameters['status'] == 'has_errors', function ($query) {
                $query->where('has_errors', 1);
            })
            ->when($parameters['status'] == 'inativo', function ($query) {
                $query->onlyTrashed();
            });
    }

    protected function dataTable(Builder $queryBuilder, array $requestData): JsonResponse
    {
        return DataTables::of($queryBuilder)
            ->order(function ($query) use ($requestData) {
                [ $order ] = $requestData['order'];

                $query->when($order['column'] == 0, fn ($q) => $q->orderBy('nome', $order['dir']))
                      ->when($order['column'] == 1, fn ($q) => $q->orderBy('congregacao', $order['dir']));              
            })
            ->addColumn('actions', function (MembresiaMembro $congregado) {
                return view('congregados.slice-actions', ['congregado' => $congregado]);
            })
            ->make(true);
    }
}
