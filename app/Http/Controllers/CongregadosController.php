<?php

namespace App\Http\Controllers;

use App\Exceptions\MembroNotFoundException;
use App\Http\Requests\StoreCongregadoRequest;
use App\Models\MembresiaMembro;
use App\DataTables\CongregadosDatatable;
use App\Services\ServiceMembrosGeral\DeletarMembroService;
use App\Services\ServiceMembrosGeral\EditarMembroService;
use App\Services\ServiceMembrosGeral\UpdateMembroService;
use App\Services\ServicesCongregados\IdentificaDadosIndexService;
use App\Services\ServicesCongregados\NovoCongregadoService;
use App\Services\ServicesCongregados\SalvarCongregadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CongregadosController extends Controller
{
    public function index() {
        $data = app(IdentificaDadosIndexService::class)->execute();
        return view('congregados.index', $data);
    }

    public function list(Request $request) {
        try {
            return app(CongregadosDatatable::class)->execute($request->all());
        } catch(\Exception $e) {
            return response()->json(['error' => 'erro ao carregar os dados dos congregados'], 500);
        }
    }

    public function novo() {
        try {
            $data = app(NovoCongregadoService::class)->execute();

            return view('congregados.novo.index', $data);
        } catch(MembroNotFoundException $e) {
            return redirect()->route('congregado.index')->with('error', 'Registro não encontrado.');
        } catch(\Exception $e) {
            return redirect()->route('congregado.index')->with('error', 'Erro ao abrir a página, por favor, tente mais tarde!');
        }
    }

    public function store(StoreCongregadoRequest $request)
    {
       try {
            DB::beginTransaction();
            $membroID = app(SalvarCongregadoService::class)->execute($request->all());
            DB::commit();
            return redirect()->action([CongregadosController::class, 'editar'], ['id' => $membroID])->with('success', 'Registro atualizado.');
        } catch(\Exception $e) {
            DB::rollback();
            report($e);
            $message = str_contains($e->getMessage(), 'S3')
                ? 'Não foi possível enviar a foto para o S3 no momento. Verifique a configuração do S3 e tente novamente.'
                : 'Falha na criação do registro.';
            return redirect()->route('congregado.index')->with('error', $message);
        }
    }

    public function update(StoreCongregadoRequest $request)
    {
       try {
            DB::beginTransaction();
            app(UpdateMembroService::class)->execute($request->all(), MembresiaMembro::VINCULO_CONGREGADO);
            DB::commit();
            return redirect()->action([CongregadosController::class, 'editar'], ['id' => $request->input('membro_id')])->with('success', 'Registro atualizado.');
        } catch(\Exception $e) {
            DB::rollback();
            report($e);
            $message = str_contains($e->getMessage(), 'S3')
                ? 'Não foi possível enviar a foto para o S3 no momento. Verifique a configuração do S3 e tente novamente.'
                : 'Falha na atualização do registro.';
            return redirect()->action([CongregadosController::class, 'editar'], ['id' => $request->input('membro_id')])->with('error', $message);
        }
    }

    public function editar($id)
    {
        try {
            $data = app(EditarMembroService::class)->findOne($id);

            return view('congregados.editar.index', $data);
        } catch(MembroNotFoundException $e) {
            return redirect()->route('visitante.index')->with('error', 'Registro não encontrado.');
        } catch(\Exception $e) {
            return redirect()->route('visitante.index')->with('error', 'Erro ao abrir a página, por favor, tente mais tarde!');
        }
    }

    public function deletar($id)
    {
        try {
            app(DeletarMembroService::class)->execute($id);
            return redirect()->route('congregado.index')->with('success', 'Registro deletado com sucesso.');
        } catch(\Exception $e) {
            return back()->with('error', 'Falha ao deletar o registro.');
        }
    }

}
