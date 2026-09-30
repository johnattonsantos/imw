<?php

namespace App\Http\Controllers;

use App\Exceptions\MembroNotFoundException;
use App\Http\Requests\StoreCongregadoRequest;
use App\Http\Requests\StoreReceberCongregadoExternoRequest;
use App\Http\Requests\StoreTransferenciaCongregadoRequest;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\MembresiaMembro;
use App\DataTables\CongregadosDatatable;
use App\Models\NotificacaoTransferencia;
use App\Services\ServiceMembrosGeral\DeletarMembroService;
use App\Services\ServiceMembrosGeral\EditarMembroService;
use App\Services\ServiceMembrosGeral\UpdateMembroService;
use App\Services\ServicesCongregados\IdentificaDadosIndexService;
use App\Services\ServicesCongregados\NovoCongregadoService;
use App\Services\ServicesCongregados\SalvarCongregadoService;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CongregadosController extends Controller
{
    use Identifiable;

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
            return redirect()->route('congregado.index')->with('error', __('Registro não encontrado.'));
        } catch(\Exception $e) {
            return redirect()->route('congregado.index')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function store(StoreCongregadoRequest $request)
    {
       try {
            DB::beginTransaction();
            $membroID = app(SalvarCongregadoService::class)->execute($request->all());
            DB::commit();
            return redirect()->action([CongregadosController::class, 'editar'], ['id' => $membroID])->with('success', __('Registro atualizado.'));
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
            return redirect()->action([CongregadosController::class, 'editar'], ['id' => $request->input('membro_id')])->with('success', __('Registro atualizado.'));
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
            return redirect()->route('visitante.index')->with('error', __('Registro não encontrado.'));
        } catch(\Exception $e) {
            return redirect()->route('visitante.index')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function deletar($id)
    {
        try {
            app(DeletarMembroService::class)->execute($id);
            return redirect()->route('congregado.index')->with('success', __('Registro deletado com sucesso.'));
        } catch(\Exception $e) {
            return back()->with('error', __('Falha ao deletar o registro.'));
        }
    }

    public function reintegrar($id)
    {
        try {
            DB::beginTransaction();

            $congregado = MembresiaMembro::withTrashed()
                ->where('id', $id)
                ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
                ->where('vinculo', MembresiaMembro::VINCULO_CONGREGADO)
                ->firstOrFail();

            $congregado->restore();
            $congregado->update(['status' => MembresiaMembro::STATUS_ATIVO]);

            DB::commit();

            return redirect()->route('congregado.index')->with('success', __('Congregado reintegrado com sucesso.'));
        } catch(\Exception $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', __('Falha ao reintegrar o congregado.'));
        }
    }

    public function transferencia($id)
    {
        try {
            $pessoa = $this->fetchCongregadoOrigem($id);
            $igrejas = $this->fetchIgrejasTransferencia();

            return view('congregados.transferencia', compact('pessoa', 'igrejas'));
        } catch(\Exception $e) {
            report($e);

            return redirect()->route('congregado.index')->with('error', __('Erro ao abrir a página de transferência.'));
        }
    }

    public function storeTransferencia(StoreTransferenciaCongregadoRequest $request, $id)
    {
        try {
            $congregado = $this->fetchCongregadoOrigem($id);
            if ($congregado->notificacaoTransferenciaAtiva()->exists()) {
                return redirect()->route('congregado.index')->with('error', __('Já existe uma transferência pendente para este congregado.'));
            }

            DB::beginTransaction();

            $instituicoesDestino = $this->fetchInstituicoesDestino((int) $request->input('igreja_id'));
            $instituicoesOrigem = session('session_perfil')->instituicoes;

            NotificacaoTransferencia::create([
                'membro_id' => $congregado->id,
                'user_abertura' => Auth::id(),
                'dt_abertura' => $request->input('dt_notificacao'),
                'regiao_origem_id' => $instituicoesOrigem->regiao->id,
                'distrito_origem_id' => $instituicoesOrigem->distrito->id,
                'igreja_origem_id' => $instituicoesOrigem->igrejaLocal->id,
                'regiao_destino_id' => $instituicoesDestino->regiao->id,
                'distrito_destino_id' => $instituicoesDestino->distrito->id,
                'igreja_destino_id' => $instituicoesDestino->igrejaLocal->id,
            ]);

            DB::commit();

            return redirect()->route('congregado.index')->with('success', __('Transferência de congregado registrada com sucesso!'));
        } catch(\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            report($e);

            return redirect()->route('congregado.transferencia', ['id' => $id])->with('error', __('Erro ao registrar a transferência.'));
        }
    }

    public function cancelTransferencia(NotificacaoTransferencia $notificacaoTransferencia)
    {
        try {
            DB::beginTransaction();

            $this->assertNotificacaoCongregadoOrigem($notificacaoTransferencia);
            $notificacaoTransferencia->delete();

            DB::commit();

            return redirect()->route('congregado.index')->with('success', __('Transferência cancelada com sucesso!'));
        } catch(\Exception $e) {
            DB::rollBack();
            report($e);

            return redirect()->route('congregado.index')->with('error', __('Erro ao cancelar a transferência.'));
        }
    }

    public function receberCongregadoExterno(NotificacaoTransferencia $notificacao)
    {
        try {
            $this->assertNotificacaoCongregadoDestino($notificacao);

            return view('congregados.receber_congregado_externo', [
                'pessoa' => $notificacao->membro,
                'congregacoes' => Identifiable::fetchCongregacoes(),
                'notificacao' => $notificacao,
            ]);
        } catch(\Exception $e) {
            report($e);

            return redirect()->back()->with('error', __('Erro ao abrir a página de recebimento de congregado externo.'));
        }
    }

    public function storeReceberCongregadoExterno(StoreReceberCongregadoExternoRequest $request, NotificacaoTransferencia $notificacao)
    {
        try {
            DB::beginTransaction();

            $this->assertNotificacaoCongregadoDestino($notificacao);

            $campoDataResposta = $request->input('action') === 'accept' ? 'dt_aceite' : 'dt_rejeicao';

            $notificacao->update([
                'user_finalizacao' => Auth::id(),
                $campoDataResposta => $request->input('dt_resposta'),
            ]);

            if ($request->input('action') === 'accept') {
                $congregado = MembresiaMembro::withTrashed()
                    ->where('id', $notificacao->membro_id)
                    ->where('vinculo', MembresiaMembro::VINCULO_CONGREGADO)
                    ->firstOrFail();

                $congregado->restore();
                $congregado->update([
                    ...Identifiable::fetchSessionInstituicoesStoreMembresia(),
                    'congregacao_id' => $request->input('congregacao_id'),
                    'status' => MembresiaMembro::STATUS_ATIVO,
                    'vinculo' => MembresiaMembro::VINCULO_CONGREGADO,
                ]);
            }

            DB::commit();

            $message = $request->input('action') === 'accept'
                ? __('Congregado externo recebido com sucesso!')
                : __('Transferência de congregado rejeitada com sucesso!');

            return redirect()->route('congregado.index')->with('success', $message);
        } catch(\Exception $e) {
            DB::rollBack();
            report($e);

            return redirect()->route('congregado.receber_congregado_externo', ['notificacao' => $notificacao->id])->with('error', __('Erro ao finalizar o recebimento do congregado externo.'));
        }
    }

    private function fetchCongregadoOrigem($id): MembresiaMembro
    {
        return MembresiaMembro::withTrashed()
            ->where('id', $id)
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->where('vinculo', MembresiaMembro::VINCULO_CONGREGADO)
            ->firstOrFail();
    }

    private function fetchIgrejasTransferencia()
    {
        $subQuery = InstituicoesInstituicao::select('id');

        return InstituicoesInstituicao::query()
            ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'instituicoes_instituicoes.instituicao_pai_id')
            ->whereIn('instituicoes_instituicoes.instituicao_pai_id', $subQuery)
            ->where('instituicoes_instituicoes.ativo', 1)
            ->where('instituicoes_instituicoes.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('instituicoes_instituicoes.id', '<>', Identifiable::fetchSessionIgrejaLocal()->id)
            ->select('instituicoes_instituicoes.*')
            ->with([
                'instituicaoPai:id,nome,instituicao_pai_id',
                'instituicaoPai.instituicaoPai:id,nome',
            ])
            ->orderBy('distrito.nome', 'asc')
            ->orderBy('instituicoes_instituicoes.nome', 'asc')
            ->get();
    }

    private function fetchInstituicoesDestino(int $igrejaId): object
    {
        $igrejaLocal = InstituicoesInstituicao::findOrFail($igrejaId);
        $distrito = InstituicoesInstituicao::where('id', $igrejaLocal->instituicao_pai_id)
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->firstOrFail();
        $regiao = InstituicoesInstituicao::where('id', $distrito->instituicao_pai_id)
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::REGIAO)
            ->firstOrFail();

        return (object) [
            'igrejaLocal' => $igrejaLocal,
            'distrito' => $distrito,
            'regiao' => $regiao,
        ];
    }

    private function assertNotificacaoCongregadoOrigem(NotificacaoTransferencia $notificacao): void
    {
        $notificacao->loadMissing('membro');

        if (
            $notificacao->dt_aceite ||
            $notificacao->dt_rejeicao ||
            (int) $notificacao->igreja_origem_id !== (int) Identifiable::fetchSessionIgrejaLocal()->id ||
            !$notificacao->membro ||
            $notificacao->membro->vinculo !== MembresiaMembro::VINCULO_CONGREGADO
        ) {
            abort(404);
        }
    }

    private function assertNotificacaoCongregadoDestino(NotificacaoTransferencia $notificacao): void
    {
        $notificacao->loadMissing('membro');

        if (
            $notificacao->dt_aceite ||
            $notificacao->dt_rejeicao ||
            (int) $notificacao->igreja_destino_id !== (int) Identifiable::fetchSessionIgrejaLocal()->id ||
            !$notificacao->membro ||
            $notificacao->membro->vinculo !== MembresiaMembro::VINCULO_CONGREGADO
        ) {
            abort(404);
        }
    }

}
