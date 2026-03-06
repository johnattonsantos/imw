<?php

namespace App\Http\Controllers;

use App\Exceptions\FinanceiroLancamentoNotFoundException;
use App\Exceptions\LancamentoNotFoundException;
use App\Http\Requests\FinanceiroStoreEntradaRequest;
use App\Http\Requests\FinanceiroStoreNewAnexoRequest;
use App\Http\Requests\FinanceiroStoreSaidaRequest;
use App\Http\Requests\FinanceiroTransferenciaRequest;
use App\Http\Requests\FinanceiroUpdateEntradaRequest;
use App\Http\Requests\FinanceiroUpdateSaidaRequest;
use App\Http\Requests\StoreNewAnexoRequest;
use App\Models\Anexo;
use App\Models\FinanceiroLancamento;
use App\Models\FinanceiroPlanoConta;
use App\Models\Mes;
use App\Services\ServiceFinanceiro\GetAnexosByLancamentoService;
use App\Services\ServiceFinanceiro\BuscarAnexosServices;
use App\Services\ServiceFinanceiro\ConsolidacaoService;
use App\Services\ServiceFinanceiro\ConsolidacaoStoreService;
use App\Services\ServiceFinanceiro\DeletarLancamentoService;
use App\Services\ServiceFinanceiro\IdentificaDadosCotaOrcamentariaDistritoService;
use App\Services\ServiceFinanceiro\IdentificaDadosCotaOrcamentariaService;
use App\Services\ServiceFinanceiro\IdentificaDadosMovimentacoesCaixaService;
use App\Services\ServiceFinanceiro\IdentificaDadosNovaMovimentacaoService;
use App\Services\ServiceFinanceiro\IdentificaDadosRecursoHumanoService;
use App\Services\ServiceFinanceiro\SaldoService;
use App\Services\ServiceFinanceiro\StoreLancamentoEntradaService;
use App\Services\ServiceFinanceiro\StoreLancamentoSaidaService;
use App\Services\ServiceFinanceiro\StoreNewAnexoService;
use App\Services\ServiceFinanceiro\StoreTransferenciaService;
use App\Services\ServiceFinanceiro\UpdateLancamentoEntradaService;
use App\Services\ServiceFinanceiro\UpdateLancamentoSaidaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinanceiroController extends Controller
{
    public function movimentocaixa(Request $request)
    {
        try {
            if($request['consolidado'] == 1){
                if($request['d1'] == '' && $request['d1'] == ''){
                    return redirect()->back()->with('error', 'Para verificar consolidados informe Data Início e Data Fim');
                }
            }
            $filters = $request->only(['caixa_id', 'plano_conta_id', 'd1', 'd2', 'consolidado']);
            $data = app(IdentificaDadosMovimentacoesCaixaService::class)->execute($filters);
            return view('financeiro.movimentocaixa', $data);
        } catch(\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página de Movimento de Caixa');
        }
    }

    public function entrada()
    {
        try {
            $data = app(IdentificaDadosNovaMovimentacaoService::class)->execute(FinanceiroPlanoConta::TP_ENTRADA);
            return view('financeiro.entrada', $data);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página de nova movimentação de entrada');
        }
    }

    public function storeEntrada(FinanceiroStoreEntradaRequest $request)
    {
        /* try { */
            DB::begintransaction();
            app(StoreLancamentoEntradaService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('financeiro.entrada')->with('success', 'Lançamento de entrada realizado.')->withInput();
        /* } catch (\Exception $e) {
            
            return redirect()->back()->with('error', 'Não foi possível criar um registro de entrada');
        } */
    }

    public function storeTransferencia(FinanceiroTransferenciaRequest $request)
    {

        try {
            DB::begintransaction();
            app(StoreTransferenciaService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('financeiro.transferencia')->with('success', 'Transferência realizada.')->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível criar um registro de transferência');
        }
    }

    public function saida()
    {
        try {
            $data = app(IdentificaDadosNovaMovimentacaoService::class)->execute(FinanceiroPlanoConta::TP_SAIDA);
            return view('financeiro.saida', $data);            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página de nova movimentação de saída');
        }
    }

    public function storeSaida(FinanceiroStoreSaidaRequest $request)
    {
        try {
            DB::begintransaction();
            app(StoreLancamentoSaidaService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('financeiro.saida')->with('success', 'Lançamento de saída realizado.')->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Não foi possível criar um registro de saída');
        }
    }

    public function transferencia()
    {
        try{
            $data = app(IdentificaDadosNovaMovimentacaoService::class)->execute(FinanceiroPlanoConta::TP_TRANSFERENCIA);
            return view('financeiro.transferencia', $data);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página de nova movimentação de transferência');
        }
    }

    public function consolidarcaixa()
    {
        try{
            $data = app(ConsolidacaoService::class)->execute();
            return view('financeiro.consolidarcaixa', $data);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página');
        }
    }

    public function consolidarstore(Request $request) {

        try {
            DB::begintransaction();
            app(ConsolidacaoStoreService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('financeiro.consolidar.caixa')->with('success', 'Consolidação realizada.')->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->back()->with('error', 'Não foi possível consolidar.');
        }
    }

    public function saldo()
    {
        try{
            $data = app(SaldoService::class)->execute();
            return view('financeiro.saldo', $data);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página');
        }
    }

       public function buscarAnexos($id) {
        $data = app(BuscarAnexosServices::class)->execute($id);
        return response()->json($data);
    }

    public function excluirMovimento($id) {
        try {
            DB::beginTransaction();
            app(DeletarLancamentoService::class)->execute($id);
            DB::commit();
            return redirect()->route('financeiro.movimento.caixa')->with('success', 'Movimento excluído com sucesso.'); 
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erro ao excluir o movimento: ' . $e->getMessage());
        }
    }

    public function editarMovimento($id, $tipo_lancamento) {
        
        $service = app(IdentificaDadosNovaMovimentacaoService::class);
        
        try {
            if ($tipo_lancamento == FinanceiroPlanoConta::TP_ENTRADA) {
                $lancamento = FinanceiroLancamento::findOrFail($id);
                $data = $this->prepareDataForView($service->execute(FinanceiroPlanoConta::TP_ENTRADA), $lancamento, 'entrada');
    
                return view('financeiro.editarentrada', $data);
                
            } elseif ($tipo_lancamento == FinanceiroPlanoConta::TP_SAIDA) {
                $lancamento = FinanceiroLancamento::findOrFail($id);
                $data = $this->prepareDataForView($service->execute(FinanceiroPlanoConta::TP_SAIDA), $lancamento, 'saida');              
                $anexos = app(BuscarAnexosServices::class)->execute($id); 
                $data['anexos'] = $anexos;

                return view('financeiro.editarsaida', $data);
                
            } else {
                return redirect()->back()->with('error', 'Tipo de movimentação não encontrado.');
            }
            
        } catch(FinanceiroLancamentoNotFoundException $e) {
            $route = $tipo_lancamento == FinanceiroPlanoConta::TP_ENTRADA ? 'financeiro.editarentrada' : 'financeiro.editarsaida';
            return redirect()->route($route)->with('error', 'Registro não encontrado.');
            
        } catch (\Exception $e) {
            $route = $tipo_lancamento == FinanceiroPlanoConta::TP_ENTRADA ? 'financeiro.editarentrada' : 'financeiro.editarsaida';
            return redirect()->route($route)->with('error', 'Erro ao abrir a página, por favor, tente mais tarde!');
        }
    }

    public function htmlManipularAnexos($lancamento)
    {
        try {
            $data = app(GetAnexosByLancamentoService::class)->execute($lancamento);
            return view('financeiro.html-edicao-anexo', $data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Não foi possível abrir a página de movimento de caixa'], 500);
        }
    }

    public function downloadAnexo(Anexo $anexo)
    {
        try {
            $fileContent = Storage::disk('s3')->get($anexo->caminho);
        
            return response($fileContent)
                ->header('Content-Type', "application/{$anexo->mime}")
                ->header('Content-Disposition', 'attachment; filename="' . $anexo->nome . '"');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Não foi possível baixar o anexo informado'], 500);
        }
    }

    public function deleteAnexo(Anexo $anexo)
    {
        try {
            DB::beginTransaction();
            $anexo->delete();
            DB::commit();
            return response()->json(['message' => 'Anexo excluído com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Não foi possível excluir o anexo informado'], 500);
        }
    }

    public function storeNewAnexo(FinanceiroStoreNewAnexoRequest $request, FinanceiroLancamento $lancamento)
    {
        try {
            DB::beginTransaction();
            app(StoreNewAnexoService::class)->execute($request->validated(), $lancamento);
            DB::commit();
            return response()->json(['message' => 'Anexo salvo com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Não foi possível salvar o anexo informado'], 500);
        }
    }  
    
    private function prepareDataForView($data, $lancamento, $key) {
        $data[$key] = $lancamento;
        return $data;
    }
    
    public function updateEntrada(FinanceiroUpdateEntradaRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(UpdateLancamentoEntradaService::class)->execute($request->all(), $id);
            DB::commit();
            return redirect()->route('financeiro.movimento.caixa')->with('success', 'Lançamento de entrada atualizado.'); 
        } catch(\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->route('financeiro.movimento.caixa')->with('error', $e->getMessage()); 
        }
    }

    public function updateSaida(FinanceiroUpdateSaidaRequest $request, $id) {
        try {
            DB::beginTransaction();
            app(UpdateLancamentoSaidaService::class)->execute($request->all(), $id);
            DB::commit();
            return redirect()->route('financeiro.movimento.caixa')->with('success', 'Lançamento de entrada atualizado.'); 
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('financeiro.movimento.caixa')->with('error', $e->getMessage()); 
        }
    }

    public function CotaOrcamentaria(Request $request)
    {
        $instituicao_id = session('session_perfil')->instituicao_id;
        $instituicao_nome = session('session_perfil')->instituicao_nome;
        if($request->mes == 1){
            $ano = $request->ano - 1;
            $mes = 12;
        }else{
            $ano = $request->ano;
            if($request->mes == null){
                $mes = $request->mes;
            }else{
                $mes = $request->mes - 1;
            }
        }
        $dados['ano'] = $ano;
        $dados['mes'] = $mes;
        $dados['instituicao_id'] = $instituicao_id;
        $dados['tipo'] = 'igreja';
        try {
            $mes = Mes::where('id',$mes)->first();
            $data = app(IdentificaDadosCotaOrcamentariaService::class)->execute($dados);
            $data['instituicao'] = $instituicao_nome;
            if(isset($mes->descricao)){
                $data['titulo'] = "COTA ORÇAMENTÁRIA - $instituicao_nome do mês de $mes->descricao de $ano";
            }else{
                $data['titulo'] = "COTA ORÇAMENTÁRIA - $instituicao_nome";
            }
            return view('financeiro.cota-orcamentaria.index', $data);
        } catch(\Exception $e) {
            //dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página cota de orçamento');
        }
    }

    public function CotaOrcamentariaDistrito(Request $request)
    {
        $instituicao_id = session('session_perfil')->instituicao_id;
        $instituicao_nome = session('session_perfil')->instituicao_nome;
        if($request->mes == 1){
            $ano = $request->ano - 1;
            $mes = 12;
        }else{
            $ano = $request->ano;
            if($request->mes == null){
                $mes = $request->mes;
            }else{
                $mes = $request->mes - 1;
            }
        }
        $dados['ano'] = $ano;
        $dados['mes'] = $mes;
        $dados['instituicao_id'] = $instituicao_id;
        $dados['tipo'] = 'distrito';
        try {
            $mes = Mes::where('id',$mes)->first();
            $data = app(IdentificaDadosCotaOrcamentariaService::class)->execute($dados);
            $data['instituicao'] = $instituicao_nome;
            if(isset($mes->descricao)){
                $data['titulo'] = "COTA ORÇAMENTÁRIA - $instituicao_nome do mês de $mes->descricao de $ano";
            }else{
                $data['titulo'] = "COTA ORÇAMENTÁRIA - $instituicao_nome";
            }
            return view('financeiro.cota-orcamentaria.distrito.index', $data);
        } catch(\Exception $e) {
            //dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página cota de orçamento');
        }
    }

    public function RecursoHumanoDistrito(Request $request)
    {
        $instituicao_id = session('session_perfil')->instituicao_id;
        $instituicao_nome = session('session_perfil')->instituicao_nome;
        if($request->mes == 1){
            $ano = $request->ano - 1;
            $mes = 12;
        }else{
            $ano = $request->ano;
            if($request->mes == null){
                $mes = $request->mes;
            }else{
                $mes = $request->mes - 1;
            }
        }
        $dados['ano'] = $ano;
        $dados['mes'] = $mes;
        $dados['instituicao_id'] = $instituicao_id;
        $dados['tipo'] = 'distrito';
        try {
            $mes = Mes::where('id',$mes)->first();
            $data = app(IdentificaDadosRecursoHumanoService::class)->execute($dados);
            $data['instituicao'] = $instituicao_nome;
            if(isset($mes->descricao)){
                $data['titulo'] = "RECURSOS HUMANOS - $instituicao_nome do mês de $mes->descricao de $ano";
            }else{
                $data['titulo'] = "RECURSOS HUMANOS - $instituicao_nome";
            }
            return view('distrito.relatorios.recurso-humano', $data);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível abrir a página cota de orçamento');
        }
    }

    public function CotaOrcamentariaRegiao(Request $request)
    {
        $instituicao_id = session('session_perfil')->instituicao_id;
        $instituicao_nome = session('session_perfil')->instituicao_nome;
        if($request->mes == 1){
            $ano = $request->ano - 1;
            $mes = 12;
        }else{
            $ano = $request->ano;
            if($request->mes == null){
                $mes = $request->mes;
            }else{
                $mes = $request->mes - 1;
            }
        }
        $dados['ano'] = $ano;
        $dados['mes'] = $mes;
        $dados['instituicao_id'] = $instituicao_id;
        $dados['tipo'] = 'regiao';
        try {
            $mes = Mes::where('id',$mes)->first();
            $data = app(IdentificaDadosCotaOrcamentariaService::class)->execute($dados);
            $data['instituicao'] = $instituicao_nome;
            if(isset($mes->descricao)){
                $data['titulo'] = "COTA ORÇAMENTÁRIA - $instituicao_nome do mês de $mes->descricao de $ano";
            }else{
                $data['titulo'] = "COTA ORÇAMENTÁRIA - $instituicao_nome";
            }
            return view('financeiro.cota-orcamentaria.regiao.index', $data);
        } catch(\Exception $e) {
            //dd($e);
            return redirect()->back()->with('error', 'Não foi possível abrir a página cota de orçamento');
        }
    }

}
