<?php

namespace App\Http\Controllers;

use App\Exceptions\IdentificaDadosExcluirMembroException;
use App\Exceptions\MembroNotFoundException;
use App\Exceptions\ReceberNovoMembroException;
use App\Exceptions\ReintegrarMembroException;
use App\Exceptions\CpfDuplicadoConfirmacaoNecessariaException;
use App\Http\Requests\DeletarMembroRequest;
use App\Http\Requests\StoreDisciplinarRequest;
use App\Http\Requests\StoreReceberNovoMembroRequest;
use App\Http\Requests\StoreReintegracaoRequest;
use App\Http\Requests\StoreExclusaoPorTransferenciaRequest;
use App\Http\Requests\StoreReceberMembroExternoRequest;
use App\Http\Requests\StoreTransferenciaInternaRequest;
use App\Http\Requests\UpdateDisciplinarRequest;
use App\Http\Requests\UpdateMembroRequest;
use App\Models\MembresiaMembro;
use App\Models\NotificacaoTransferencia;
use App\DataTables\RolMembroDatatable;
use App\DataTables\RolMembroRecadastramentoDatatable;
use App\Services\ServiceMembros\DeletarMembroService;
use App\Services\ServiceMembros\IdentificaDadosDisciplinaService;
use App\Services\ServiceMembros\IdentificaDadosExcluirMembroService;
use App\Services\ServiceMembros\IdentificaDadosIndexRecadastramentoService;
use App\Services\ServiceMembros\IdentificaDadosIndexService;
use App\Services\ServiceMembros\IdentificaDadosReceberMembroExternoService;
use App\Services\ServiceMembros\IdentificaDadosReceberNovoMembroService;
use App\Services\ServiceMembros\IdentificaDadosReintegrarMembroService;
use App\Services\ServiceMembros\IdentificaDadosTransferenciaInternaService;
use App\Services\ServiceMembros\IdentificaDadosTransferenciaPorExclusaoService;
use App\Services\ServiceMembros\ListDisciplinasMembroService;
use App\Services\ServiceMembros\StoreDiciplinaService;
use App\Services\ServiceMembros\StoreNotificacaoExclusaoPorTransferenciaService;
use App\Services\ServiceMembros\StoreReceberMembroExternoService;
use App\Services\ServiceMembros\StoreReceberNovoMembroService;
use App\Services\ServiceMembros\StoreReintegracaoService;
use App\Services\ServiceMembros\StoreTransferenciaInternaService;
use App\Services\ServiceMembros\ConsultaCpfMembroService;
use App\Services\ServiceMembros\UpdateDisciplinarService;
use App\Services\ServiceMembrosGeral\EditarMembroRecadastramentoService;
use App\Services\ServiceMembrosGeral\EditarMembroService;
use App\Services\ServiceMembrosGeral\UpdateMembroRecadastramentoService;
use App\Services\ServiceMembrosGeral\UpdateMembroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MembrosController extends Controller
{
    public function index()
    {
        $data = app(IdentificaDadosIndexService::class)->execute();
        return view('membros.index', $data);
    }

    public function indexRecadastramento()
    {
        $data = app(IdentificaDadosIndexRecadastramentoService::class)->execute();
        return view('membros.index_recadastramento', $data);
    }

    public function listRecadastramento(Request $request)
    {
        try {
            return app(RolMembroRecadastramentoDatatable::class)->execute($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => 'erro ao carregar os dados dos membros'], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            return app(RolMembroDatatable::class)->execute($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => 'erro ao carregar os dados dos membros'], 500);
        }
    }

    public function editar($id)
    {
        try {
            $pessoa = app(EditarMembroService::class)->findOne($id);
            $disciplinas = app(ListDisciplinasMembroService::class)->execute($id);

            return view('membros.editar.index', [
                'pessoa'               => $pessoa['pessoa'],
                'ministerios'          => $pessoa['ministerios'],
                'funcoes'              => $pessoa['funcoes'],
                'cursos'               => $pessoa['cursos'],
                'formacoes'            => $pessoa['formacoes'],
                'profissoes'           => $pessoa['profissoes'],
                'funcoesEclesiasticas' => $pessoa['funcoesEclesiasticas'],
                'congregacoes'         => $pessoa['congregacoes'],
                'modosRecepcao'        => $pessoa['modosRecepcao'],
                'modosExclusao'        => $pessoa['modosExclusao'],
                'disciplinas'          => $disciplinas,
                'gceus'                => $pessoa['gceus'],
                'gceuFuncoes'          => $pessoa['gceuFuncoes'],
                'gceuMembros'          => $pessoa['gceuMembros']
            ]);
        } catch(MembroNotFoundException $e) {
            return redirect()->route('membro.index')->with('error', __('Registro não encontrado.'));
        } catch(\Exception $e) {
            report($e);
            return redirect()->route('membro.index')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }
    public function editarRecadastramento($id)
    {
        try {
            $pessoa = app(EditarMembroRecadastramentoService::class)->findOne($id);
            $disciplinas = app(ListDisciplinasMembroService::class)->execute($id);
            return view('membros.editar.indexRecadastramento', [
                'pessoa'               => $pessoa['pessoa'],
                'ministerios'          => $pessoa['ministerios'],
                'funcoes'              => $pessoa['funcoes'],
                'cursos'               => $pessoa['cursos'],
                'formacoes'            => $pessoa['formacoes'],
                'profissoes'           => $pessoa['profissoes'],
                'funcoesEclesiasticas' => $pessoa['funcoesEclesiasticas'],
                'congregacoes'         => $pessoa['congregacoes'],
                'modosRecepcao'        => $pessoa['modosRecepcao'],
                'modosExclusao'        => $pessoa['modosExclusao'],
                'disciplinas'          => $disciplinas,
                'gceus'                => $pessoa['gceus'],
                'gceuFuncoes'          => $pessoa['gceuFuncoes'],
                'gceuMembros'          => $pessoa['gceuMembros']
            ]);
        } catch(MembroNotFoundException $e) {
            return redirect()->route('recadastramento-membro.indexRecadastramento')->with('error', __('Registro não encontrado.'));
        } catch(\Exception $e) {
            report($e);
            return redirect()->route('recadastramento-membro.indexRecadastramento')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function visualizarHtml($id)
    {
        try {
            return view('membresiaGeral.visualizar', ['membro' => MembresiaMembro::findOrFail($id)]);
        } catch(\Exception $e) {
            return response()->json(['message' => 'Erro ao visualizar dados desta pessoa.'], 500);
        }
    }

    public function update(UpdateMembroRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(UpdateMembroService::class)->execute($request->all(), MembresiaMembro::VINCULO_MEMBRO);
            DB::commit();
            return redirect()->action([MembrosController::class, 'editar'], ['id' => $request->input('membro_id')])->with('success', __('Registro atualizado.'));
        } catch(\Exception $e) {
            DB::rollback();
            report($e);
            $message = str_contains($e->getMessage(), 'S3')
                ? 'Não foi possível enviar a foto para o S3 no momento. Verifique a configuração do S3 e tente novamente.'
                : 'Falha na atualização do registro.';
            return redirect()->action([MembrosController::class, 'editar'], ['id' => $request->input('membro_id')])->with('error', $message);
        }
    }

    public function updateRecadastramento(UpdateMembroRequest $request, $id)
    {
        try {
            $interrupcaoCpfInativo = $this->interromperValidacaoPorCpfInativoOutraIgreja($request);
            if ($interrupcaoCpfInativo) {
                return $interrupcaoCpfInativo;
            }

            DB::beginTransaction();
            app(UpdateMembroRecadastramentoService::class)->execute($request->all(), MembresiaMembro::VINCULO_MEMBRO);
            DB::commit();
            return redirect()->route('recadastramento-membro.indexRecadastramento')->with('success', __('Registro validado com sucesso.'));
        } catch(CpfDuplicadoConfirmacaoNecessariaException $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('cpf_duplicado_confirmacao', [
                    'message' => $e->getMessage(),
                    'membro_id' => $e->membro()->id,
                ]);
        } catch(\Exception $e) {
            DB::rollback();
            report($e);
            $message = str_contains($e->getMessage(), 'S3')
                ? 'Não foi possível enviar a foto para o S3 no momento. Verifique a configuração do S3 e tente novamente.'
                : 'Falha na validação do registro.';
            return redirect()->route('recadastramento-membro.indexRecadastramento')->with('error', $message);
        }
    }

    private function interromperValidacaoPorCpfInativoOutraIgreja(UpdateMembroRequest $request)
    {
        if ($request->input('status') !== MembresiaMembro::STATUS_ATIVO) {
            return null;
        }

        $cpf = preg_replace('/[^0-9]/', '', (string) $request->input('cpf'));
        if ($cpf === '') {
            return null;
        }

        $membroMigracaoId = $request->input('membro_id');
        $igrejaDestinoId = DB::table('membresia_migracao')
            ->where('id', $membroMigracaoId)
            ->value('igreja_id');

        if (empty($igrejaDestinoId)) {
            return null;
        }

        $consultaCpf = app(ConsultaCpfMembroService::class);
        $membroInativo = $consultaCpf->findMembroInativo($cpf, $membroMigracaoId);

        if (!$membroInativo || !$consultaCpf->isOutraIgreja($membroInativo, (int) $igrejaDestinoId)) {
            return null;
        }

        $cpfInativoConfirmado = (string) $request->input('confirmar_cpf_inativo_outra_igreja') === '1'
            && (string) $request->input('cpf_membro_existente_id') === (string) $membroInativo->id;

        if (!$cpfInativoConfirmado) {
            return redirect()
                ->back()
                ->withInput($request->except(['foto']))
                ->with('cpf_duplicado_confirmacao', [
                    'message' => $consultaCpf->mensagemConfirmacaoInativoOutraIgreja($membroInativo),
                    'membro_id' => $membroInativo->id,
                ]);
        }

        $dataExclusao = $consultaCpf->dataDesligamento($membroInativo);
        $dataRecepcao = $request->input('dt_recepcao');

        if (!$dataExclusao || !$dataRecepcao || !\Carbon\Carbon::parse($dataRecepcao)->gt($dataExclusao)) {
            return redirect()
                ->back()
                ->withInput($request->except(['foto']))
                ->with('cpf_recepcao_invalida_message', $consultaCpf->mensagemDataRecepcaoInvalida($membroInativo));
        }

        return null;
    }

    public function receberNovo($id)
    {
        try {
            $data = app(IdentificaDadosReceberNovoMembroService::class)->execute($id);

            $pessoa       = $data['pessoa'];
            $sugestaoRol  = $pessoa['rol_atual'] ? $pessoa['rol_atual'] : $data['sugestao_rol'];
            $pastores     = $data['pastores'];
            $modos        = $data['modos'];
            $congregacoes = $data['congregacoes'];
            return view('membros.receber_novo', compact('pessoa', 'sugestaoRol', 'pastores', 'modos', 'congregacoes'));
        } catch(ReceberNovoMembroException $e) {
            return redirect()->back()->with('error', __('Esta pessoa não existe na base de dados ou não pode ser recebida como Membro'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao exibir a página solicitada'));
        }
    }

    public function storeReceberNovo(StoreReceberNovoMembroRequest $request, $id)
    {
        try {
            $data = app(StoreReceberNovoMembroService::class)->execute($request->all(), $id);
            if($data == 'idade'){
                return redirect()->back()->with('error', __('Não pode ser membro, pois a idade desse congregado é menor que 12 anos'));
            }else{
                return redirect()->route('membro.editar', ['id' => $id])->with('success', __('Novo membro recebido com sucesso!'));
            }
        } catch(\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao tentar receber novo membro'));
        }
    }

    public function exclusao($id)
    {
        try {
            $data = app(IdentificaDadosExcluirMembroService::class)->execute($id);
            $pessoa       = $data['pessoa'];
            $pastores     = $data['pastores'];
            $modos        = $data['modos'];

            return view('membros.exclusao', compact('pessoa',  'pastores', 'modos'));
        } catch(IdentificaDadosExcluirMembroException $e) {
            return redirect()->back()->with('error', __('Erro ao tentar abrir a tela de exclusão de membro'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao tentar abrir a tela de exclusão de membro'));
        }
    }

    public function storeExclusao(DeletarMembroRequest $request, $id)
    {
        try {
            app(DeletarMembroService::class)->execute($request->all(), $id);

            return redirect()->route('membro.index')->with('success', __('Membro excluído com sucesso!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Houve um erro tentar excluir este membro'));
        }
    }

    public function reintegrar($id)
    {
        try {
            $data = app(IdentificaDadosReintegrarMembroService::class)->execute($id);

            $pessoa       = $data['pessoa'];
            $sugestaoRol  = $data['sugestao_rol'];
            $pastores     = $data['pastores'];
            $modos        = $data['modos'];
            $congregacoes = $data['congregacoes'];

            return view('membros.reintegrar', compact('pessoa', 'sugestaoRol', 'pastores', 'modos', 'congregacoes'));
        } catch (ReintegrarMembroException $e) {
            return redirect()->back()->with('error', __('Membro não identificado ou é um membro excluído'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao abrir a página de reintegrar membro desligado'));
        }
    }

    public function storeReintegracao(StoreReintegracaoRequest $request, $id)
    {
        try {
            app(StoreReintegracaoService::class)->execute($request->all(), $id);

            return redirect()->route('membro.editar', ['id' => $id])->with('success', __('Membro reintegrado com sucesso!'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao tentar reintegrar o membro'));
        }
    }

    public function transferenciaInterna($id)
    {
        try {
            $data = app(IdentificaDadosTransferenciaInternaService::class)->execute($id);

            $pessoa       = $data['pessoa'];
            $congregacoes = $data['congregacoes'];
            $pastores     = $data['pastores'];

            return view('membros.transferencia_interna', compact('pessoa', 'congregacoes', 'pastores'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao abrir a página de Transferência Interna'));
        }
    }

    public function storeTransferenciaInterna(StoreTransferenciaInternaRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(StoreTransferenciaInternaService::class)->execute($request->all(), $id);
            DB::commit();
            return(redirect()->route('membro.editar', ['id'=> $id])->with('success', __('Transferência Interna realizada com sucesso.')));
        } catch(\Exception $e) {
            DB::rollback();
            return(redirect()->route('membro.transferencia_interna', ['id'=> $id])->with('error', __('Erro ao realizar a tranferência interna.')));
        }
    }

    public function exclusaoPorTransferencia($id)
    {
        try {
            $data = app(IdentificaDadosTransferenciaPorExclusaoService::class)->execute($id);

            return view('membros.exclusao_transferencia', $data);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao abrir a página de Transferência Por Exclusão'));
        }
    }

    public function storeExclusaoPorTransferencia(StoreExclusaoPorTransferenciaRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(StoreNotificacaoExclusaoPorTransferenciaService::class)->execute($request->all(), $id);
            DB::commit();
            return redirect()->route('membro.editar', ['id' => $id])->with('success', __('Exclusão por transferência registrada com sucesso!'));
        } catch (\Exception $e) {
            return redirect()->route('membro.exclusao_transferencia', ['id' => $id])->with('error', __('Erro ao registrar a transferência.'));
        }
    }

    public function cancelExclusaoPorTransferencia(NotificacaoTransferencia $notificacaoTransferencia)
    {
        try {
            DB::beginTransaction();
            $notificacaoTransferencia->delete();
            DB::commit();
            return redirect()->route('membro.index')->with('success', __('Transferência cancelada com sucesso!'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('membro.index')->with('error', __('Erro ao cancelar a transferência.'));
        }
    }

    public function disciplinar($id)
    {
        try {
            $data = app(IdentificaDadosDisciplinaService::class)->execute($id);

            $pessoa   = $data['pessoa'];
            $pastores = $data['pastores'];
            $modos    = $data['modos'];

            return view('membros.disciplinar', compact('pessoa', 'pastores', 'modos'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao abrir a página de Disciplinar'));
        }
    }

    public function storeDisciplinar(StoreDisciplinarRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(StoreDiciplinaService::class)->execute($request->all(), $id);
            DB::commit();
            return(redirect()->route('membro.editar', ['id'=> $id])->with('success', __('Membro diciplinado com sucesso.')));
        } catch(\Exception $e) {
            DB::rollback();
            return(redirect()->route('membro.editar', ['id'=> $id])->with('error', __('Falha ao diciplinar o membro.')));
        }
    }

    public function updateDisciplinar(UpdateDisciplinarRequest $request, $id)
    {
        try{
            DB::beginTransaction();
            app(UpdateDisciplinarService::class)->execute($request->get('dt_termino'), $id);
            DB::commit();
            return response()->json(['message' => 'Disciplina atualizada com sucesso!']);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ao atualizar a disciplina deste membro!']);
        }
    }

    public function receberMembroExterno(NotificacaoTransferencia $notificacao)
    {
        try {
            $data = app(IdentificaDadosReceberMembroExternoService::class)->execute($notificacao);

            return view('membros.receber_membro_externo', $data);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', __('Erro ao abrir a página de recebimento de membro externo'));
        }
    }

    public function storeReceberMembroExterno(StoreReceberMembroExternoRequest $request, NotificacaoTransferencia $notificacao)
    {
        try {
            DB::beginTransaction();
            app(StoreReceberMembroExternoService::class)->execute($request->all(), $notificacao);
            DB::commit();
            return redirect()->route('membro.editar', ['id' => $notificacao->membro_id])->with('success', __('Membro externo recebido com sucesso!'));
        } catch (\Exception $e) {
            return redirect()->route('membro.receber_membro_externo', ['notificacao' => $notificacao->id])->with('error', __('Erro ao finalizar o recebimento do membro externo'));
        }
    }
}
