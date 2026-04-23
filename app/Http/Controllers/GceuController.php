<?php

namespace App\Http\Controllers;

use App\DataTables\GCeuDatatable;
use App\Http\Requests\StoreGCeuCartaPastoralRequest;
use App\Http\Requests\StoreGCeuRequest;
use App\Http\Requests\UpdateGCeuCartaPastoralRequest;
use App\Http\Requests\UpdateGCeuRequest;
use App\Models\GCeu;
use App\Models\GCeuReuniaoPessoa;
use App\Models\MembresiaMembro;
use App\Models\PessoasPessoa;
use App\Services\ServiceGCeu\CartaPastoralGCeuDistritoService;
use App\Services\ServiceGCeu\CartaPastoralGCeuRegiaoService;
use App\Services\ServiceGCeu\CartaPastoralGCeuService;
use App\Services\ServiceGCeu\DeletarGCeuCartaPastoralService;
use App\Services\ServiceGCeu\DeletarGCeuService;
use App\Services\ServiceGCeu\EditarGCeuCartaPastoralService;
use App\Services\ServiceGCeu\EditarGCeuService;
use App\Services\ServiceGCeu\GCeuDiarioPresencaFaltaService;
use App\Services\ServiceGCeu\GCeuDiarioService;
use App\Services\ServiceGCeu\GCeuMembrosService;
use App\Services\ServiceGCeu\GCeuRelatorioAniversariantesService;
use App\Services\ServiceGCeu\GCeuRelatorioDistritoAniversariantesService;
use App\Services\ServiceGCeu\GCeuRelatorioDistritoFuncoesService;
use App\Services\ServiceGCeu\GCeuRelatorioDistritoGceuService;
use App\Services\ServiceGCeu\GCeuRelatorioFuncoesService;
use App\Services\ServiceGCeu\GCeuRelatorioRegiaoFuncoesService;
use App\Services\ServiceGCeu\GCeuService;
use App\Services\ServiceGCeu\GCeuRelatorioGceuService;
use App\Services\ServiceGCeu\GCeuRelatorioRegiaoAniversariantesService;
use App\Services\ServiceGCeu\GCeuRelatorioRegiaoGceuService;
use App\Services\ServiceGCeu\GCeuUpdateMembroService;
use App\Services\ServiceGCeu\StoreGCeuCartaPastoralService;
use App\Services\ServiceGCeu\StoreGCeuService;
use App\Services\ServiceGCeu\VisualizarGCeuCartaPastoralService;
use App\Services\ServiceGCeu\VisualizarGCeuService;
use App\Services\ServiceVisitantes\IdentificaDadosIndexService;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class GceuController extends Controller
{
    use Identifiable;
    ////////////////////////////GCEU IGREJA/////////////////////////////
    public function index(Request $request)
    {
        $data = app(IdentificaDadosIndexService::class)->execute($request->all());
        return view('gceu.index', $data);
    }

    public function list(Request $request)
    {
        try {
            return app(GCeuDatatable::class)->execute($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'erro ao carregar os dados GCEU'], 500);
        }
    }

    public function editar($id)
    {
        $gceu = app(EditarGCeuService::class)->findOne($id);
        $congregacoes = Identifiable::fetchCongregacoes();
        if (!$gceu) {
            return redirect()->route('gceu.index')->with('error', 'GCEU não encontrado.');
        }
        return view('gceu.editar', compact('gceu', 'congregacoes'));
    }

    public function update(UpdateGCeuRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(EditarGCeuService::class)->execute($id, $request->all());
            DB::commit();
            return redirect()->route('gceu.editar', ['id' => $id])->with('success', 'GCEU atualizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->route('gceu.editar', ['id' => $id])->with('error', 'Falha ao atualizar o GCEU.');
        }
    }

    public function deletar($id)
    {
        $congregacoes = Identifiable::fetchCongregacoes();
        try {
            $existe = GCeu::join('gceu_membros', 'gceu_membros.gceu_cadastro_id','gceu_cadastros.id')->where('gceu_cadastros.id',$id)->first();
            if($existe){
                return back()->with('error', 'Não poderá remover esse CGEU, pois existe membros vinculados.');
            }else{
                app(DeletarGCeuService::class)->execute($id);
                return redirect()->route('gceu.index')->with('success', 'GCEU deletado com sucesso.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao deletar o GCEU.');
        }
    }

    public function novo()
    {
        try {
            return view('gceu.create', ['congregacoes' => Identifiable::fetchCongregacoes(), 'instituicao_id' => Identifiable::fetchSessionIgrejaLocal()->id]);
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao abrir a página de novo visitante');
        }
    }

    public function store(StoreGCeuRequest $request)
    {
        try {
            DB::beginTransaction();
            app(StoreGCeuService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('gceu.index')->with('success', 'GCEU cadastrado com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('gceu.index')->with('error', $e->getMessage());
        }
    }

    public function visualizarHtml($id)
    {
        $gceu = app(VisualizarGCeuService::class)->findOne($id);
        if (!$gceu) {
            return redirect()->route('gceu.index')->with('error', 'GCEU não encontrado.');
        }
        return view('gceu.visualizar', ['gceu' =>  $gceu]);
    }

    public function membros(Request $request)
    {
        //dd($request->all());
        $data = $request->all();
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(GCeuMembrosService::class)->getList($igrejaId, $data);

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.membros.index', $data);
    }

    public function reuniaoPessoas(Request $request)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $gceus = GCeu::where([
            'instituicao_id' => $igrejaId,
            'status' => GCeu::STATUS_ATIVO,
        ])->orderBy('nome', 'asc')->get();

        $gceuId = $request->input('filtro_gceu_id');
        $tipo = $request->input('filtro_tipo');
        $dataReuniao = $request->input('filtro_data_reuniao');

        $registros = GCeuReuniaoPessoa::query()
            ->select('gceu_reuniao_pessoas.*', 'gceu_cadastros.nome as gceu_nome')
            ->join('gceu_cadastros', 'gceu_cadastros.id', '=', 'gceu_reuniao_pessoas.gceu_cadastro_id')
            ->where('gceu_reuniao_pessoas.instituicao_id', $igrejaId)
            ->when($gceuId, function ($query) use ($gceuId) {
                $query->where('gceu_reuniao_pessoas.gceu_cadastro_id', $gceuId);
            })
            ->when(in_array($tipo, ['V', 'N'], true), function ($query) use ($tipo) {
                $query->where('gceu_reuniao_pessoas.tipo', $tipo);
            })
            ->when($dataReuniao, function ($query) use ($dataReuniao) {
                $query->whereDate('gceu_reuniao_pessoas.data_reuniao', $dataReuniao);
            })
            ->orderByDesc('gceu_reuniao_pessoas.data_reuniao')
            ->orderByDesc('gceu_reuniao_pessoas.created_at')
            ->get();

        return view('gceu.reuniao-pessoas.index', [
            'igreja' => Identifiable::fetchSessionIgrejaLocal()->nome,
            'gceus' => $gceus,
            'registros' => $registros,
        ]);
    }

    public function storeReuniaoPessoas(Request $request)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $payload = $request->validate([
            'gceu_cadastro_id' => ['required', 'integer'],
            'nome' => ['required', 'string', 'max:150'],
            'contato' => ['nullable', 'string', 'max:20'],
            'tipo' => ['required', 'in:V,N'],
            'data_reuniao' => ['required', 'date'],
        ]);

        $gceu = GCeu::where('id', $payload['gceu_cadastro_id'])
            ->where('instituicao_id', $igrejaId)
            ->where('status', GCeu::STATUS_ATIVO)
            ->first();

        if (!$gceu) {
            return back()->withInput()->with('error', 'GCEU inválido para a igreja logada.');
        }

        GCeuReuniaoPessoa::create([
            'gceu_cadastro_id' => $payload['gceu_cadastro_id'],
            'instituicao_id' => $igrejaId,
            'nome' => trim($payload['nome']),
            'contato' => !empty($payload['contato']) ? trim($payload['contato']) : null,
            'tipo' => $payload['tipo'],
            'data_reuniao' => $payload['data_reuniao'],
        ]);

        return redirect()->route('gceu.reuniao-pessoas')->with('success', 'Pessoa da reunião cadastrada com sucesso.');
    }

    public function marcarNovoConvertidoReuniaoPessoa($id)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $registro = GCeuReuniaoPessoa::where('id', $id)
            ->where('instituicao_id', $igrejaId)
            ->first();

        if (!$registro) {
            return back()->with('error', 'Registro não encontrado para a igreja logada.');
        }

        $registro->update(['tipo' => GCeuReuniaoPessoa::TIPO_NOVO_CONVERTIDO]);

        return back()->with('success', 'Registro atualizado para Novo Convertido.');
    }

    public function deletarReuniaoPessoa($id)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $registro = GCeuReuniaoPessoa::where('id', $id)
            ->where('instituicao_id', $igrejaId)
            ->first();

        if (!$registro) {
            return back()->with('error', 'Registro não encontrado para a igreja logada.');
        }

        $registro->delete();
        return back()->with('success', 'Registro removido com sucesso.');
    }

    public function relatorioReuniaoPessoas(Request $request)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $gceus = GCeu::where([
            'instituicao_id' => $igrejaId,
            'status' => GCeu::STATUS_ATIVO,
        ])->orderBy('nome', 'asc')->get();

        $gceuId = $request->input('gceu_id');
        $tipo = $request->input('tipo');
        $dataInicial = $request->input('data_inicial');
        $dataFinal = $request->input('data_final');

        $novoConvertidoExpr = "UPPER(COALESCE(mm.novo_convertido, '')) IN ('1','S','SIM','Y','TRUE')";

        $membresiaQuery = DB::table('gceu_membros as gm')
            ->join('gceu_cadastros as gc', 'gc.id', '=', 'gm.gceu_cadastro_id')
            ->join('membresia_membros as mm', 'mm.id', '=', 'gm.membro_id')
            ->leftJoin('membresia_contatos as mc', 'mc.membro_id', '=', 'mm.id')
            ->where('gc.instituicao_id', $igrejaId)
            ->where('gc.status', GCeu::STATUS_ATIVO)
            ->when($gceuId, function ($query) use ($gceuId) {
                $query->where('gm.gceu_cadastro_id', $gceuId);
            })
            ->when($dataInicial, function ($query) use ($dataInicial) {
                $query->whereDate('gm.created_at', '>=', $dataInicial);
            })
            ->when($dataFinal, function ($query) use ($dataFinal) {
                $query->whereDate('gm.created_at', '<=', $dataFinal);
            })
            ->when($tipo === 'C', function ($query) {
                $query->where('mm.vinculo', 'C');
            })
            ->when($tipo === 'V', function ($query) use ($novoConvertidoExpr) {
                $query->where('mm.vinculo', 'V')->whereRaw("NOT ($novoConvertidoExpr)");
            })
            ->when($tipo === 'N', function ($query) use ($novoConvertidoExpr) {
                $query->whereRaw($novoConvertidoExpr);
            })
            ->when(!in_array($tipo, ['C', 'V', 'N'], true), function ($query) use ($novoConvertidoExpr) {
                $query->where(function ($sub) use ($novoConvertidoExpr) {
                    $sub->whereIn('mm.vinculo', ['C', 'V'])
                        ->orWhereRaw($novoConvertidoExpr);
                });
            })
            ->select([
                DB::raw("'Membresia' as origem"),
                'gc.nome as gceu_nome',
                'mm.nome as nome',
                DB::raw("CASE
                    WHEN mc.telefone_preferencial IS NOT NULL AND mc.telefone_preferencial <> '' THEN mc.telefone_preferencial
                    WHEN mc.telefone_alternativo IS NOT NULL AND mc.telefone_alternativo <> '' THEN mc.telefone_alternativo
                    ELSE mc.telefone_whatsapp
                END as contato"),
                DB::raw("CASE
                    WHEN $novoConvertidoExpr THEN 'Novo Convertido'
                    WHEN mm.vinculo = 'C' THEN 'Congregado'
                    WHEN mm.vinculo = 'V' THEN 'Visitante'
                    ELSE 'Não informado'
                END as tipo"),
                DB::raw('DATE(gm.created_at) as data_reuniao'),
                DB::raw('gm.created_at as data_cadastro'),
            ]);

        $reuniaoQuery = DB::table('gceu_reuniao_pessoas as grp')
            ->join('gceu_cadastros as gc', 'gc.id', '=', 'grp.gceu_cadastro_id')
            ->where('grp.instituicao_id', $igrejaId)
            ->when($gceuId, function ($query) use ($gceuId) {
                $query->where('grp.gceu_cadastro_id', $gceuId);
            })
            ->when($dataInicial, function ($query) use ($dataInicial) {
                $query->whereDate('grp.data_reuniao', '>=', $dataInicial);
            })
            ->when($dataFinal, function ($query) use ($dataFinal) {
                $query->whereDate('grp.data_reuniao', '<=', $dataFinal);
            })
            ->when(in_array($tipo, ['V', 'N'], true), function ($query) use ($tipo) {
                $query->where('grp.tipo', $tipo);
            })
            ->when($tipo === 'C', function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->select([
                DB::raw("'Reunião' as origem"),
                'gc.nome as gceu_nome',
                'grp.nome as nome',
                'grp.contato as contato',
                DB::raw("CASE
                    WHEN grp.tipo = 'N' THEN 'Novo Convertido'
                    ELSE 'Visitante'
                END as tipo"),
                'grp.data_reuniao as data_reuniao',
                'grp.created_at as data_cadastro',
            ]);

        $dados = DB::query()
            ->fromSub($membresiaQuery->unionAll($reuniaoQuery), 'itens')
            ->orderByDesc('data_reuniao')
            ->orderByDesc('data_cadastro')
            ->get();

        return view('gceu.relatorio-igreja.reuniao-pessoas', [
            'igreja' => Identifiable::fetchSessionIgrejaLocal()->nome,
            'gceus' => $gceus,
            'dados' => $dados,
            'titulo' => 'Relatório de Visitantes, Congregados e Novos Convertidos por Reunião de GCEU',
        ]);
    }

    public function updateMembro(Request $request, $id)
    {
        DB::beginTransaction();
        app(GCeuUpdateMembroService::class)->execute($request->all(), $id);
        DB::commit();
        return back()->with('success', 'Registro atualizado.');

    }

    public function cartaPastoral()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(CartaPastoralGCeuService::class)->getList($igrejaId);
        if (!$data) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral.index', $data);
    }

    public function cartaPastoralRelatorio()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(CartaPastoralGCeuService::class)->getList($igrejaId);
        if (!$data) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral.relatorio', $data);
    }
    
    public function cartaPastoralEditar($id)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data['pastores'] = PessoasPessoa::select('pessoas_pessoas.id', 'pessoas_pessoas.nome')
                ->join('pessoas_nomeacoes', 'pessoas_nomeacoes.pessoa_id', 'pessoas_pessoas.id')
                ->join('pessoas_funcaoministerial', 'pessoas_funcaoministerial.id', 'pessoas_nomeacoes.funcao_ministerial_id')
                ->where(['pessoas_nomeacoes.instituicao_id' => $igrejaId])->whereIn('pessoas_funcaoministerial.ordem', [3,4,5])->whereNull('pessoas_nomeacoes.data_termino')->get();
        $data['cartaPastoral'] = app(EditarGCeuCartaPastoralService::class)->findOne($id);
        if (!$data['cartaPastoral']) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta Pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral.editar', $data);
    }

    public function cartaPastoralUpdate(UpdateGCeuCartaPastoralRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(EditarGCeuCartaPastoralService::class)->execute($id, $request->all());
            DB::commit();
            return redirect()->route('gceu.carta-pastoral', ['id' => $id])->with('success', 'Carta Pastoral atualizada com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->route('gceu.carta-pastoral.editar', ['id' => $id])->with('error', 'Falha ao atualizar a Carta Pastoral.');
        }
    }

    public function cartaPastoralDeletar($id)
    {
        try {
            app(DeletarGCeuCartaPastoralService::class)->execute($id);
            return redirect()->route('gceu.carta-pastoral')->with('success', 'Carta pastoral deletada com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao deletar a carta pastoral.');
        }
    }

    public function cartaPastoralNovo()
    {
        try {
            $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
            $data['pastores'] = PessoasPessoa::select('pessoas_pessoas.id', 'pessoas_pessoas.nome')
                    ->join('pessoas_nomeacoes', 'pessoas_nomeacoes.pessoa_id', 'pessoas_pessoas.id')
                    ->join('pessoas_funcaoministerial', 'pessoas_funcaoministerial.id', 'pessoas_nomeacoes.funcao_ministerial_id')
                    ->where(['pessoas_nomeacoes.instituicao_id' => $igrejaId])->whereIn('pessoas_funcaoministerial.ordem', [3,4,5])->whereNull('pessoas_nomeacoes.data_termino')->get();
            $data['instituicao_id'] = $igrejaId;
            $data['instituicao'] = Identifiable::fetchSessionIgrejaLocal()->nome;
            return view('gceu.carta-pastoral.create', $data);
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao abrir a página nova carta pastoral');
        }
    }

    public function cartaPastoralStore(StoreGCeuCartaPastoralRequest $request)
    {
        try {
            DB::beginTransaction();
            app(StoreGCeuCartaPastoralService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('gceu.carta-pastoral')->with('success', 'Carta pastoral cadastrada com sucesso.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('gceu.carta-pastoral')->with('error', $e->getMessage());
        }
    }

    public function cartaPastoralUploadImage(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = now()->format('Ymd_His') . '_' . Str::uuid() . '.' . $extension;
        $path = 'gceu/carta-pastoral/' . date('Y/m') . '/' . $filename;

        $this->editorDisk()->put($path, file_get_contents($file));
        $token = rtrim(strtr(base64_encode($path), '+/', '-_'), '=');

        return response()->json([
            'location' => URL::signedRoute('gceu.carta-pastoral.image', ['token' => $token]),
        ]);
    }

    public function cartaPastoralImage(Request $request, string $token)
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $base64 = strtr($token, '-_', '+/');
        $padding = strlen($base64) % 4;
        if ($padding > 0) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        $path = base64_decode($base64, true);
        $disk = $this->editorDisk();

        if (! is_string($path) || $path === '' || ! $disk->exists($path)) {
            abort(404);
        }

        $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';
        $content = $disk->get($path);

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function editorDisk()
    {
        return Storage::disk((string) Config::get('filesystems.editor_disk', 's3'));
    }

    public function cartaPastoralVisualizarHtml($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral.visualizar', ['cartaPastoral' =>  $cartaPastoral]);
    }

    public function cartaPastoralPdf($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        //return view('gceu.carta-pastoral.visualizar', ['cartaPastoral' =>  $cartaPastoral]);

        $pdf = FacadePdf::loadView('gceu.carta-pastoral.pdf', ['cartaPastoral' =>  $cartaPastoral]);
        return $pdf->stream('carta-pastoral-'.$cartaPastoral->titulo.'.pdf');
    }

    public function diario(Request $request)
    {
        $data = $request->all();
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(GCeuDiarioService::class)->getList($igrejaId, $data);
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.diario.index', $data);
    }

     public function diarioRelatorio(Request $request)
    {
        $data = $request->all();
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(GCeuDiarioService::class)->getListRelatorio($igrejaId, $data);
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Diário não encontrada.');
        }
        return view('gceu.diario.relatorio', $data);
    }
    

    public function diarioPresencaFalta(Request $request)
    {
        $data = $request->all();
        $data = app(GCeuDiarioPresencaFaltaService::class)->salvarDiario($data);
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.diario.index', $data);
    }

    public function gceuRelatorioFuncoes()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $tipo = request()->tipo;
        if (!in_array($tipo, ['M', 'C', 'V', 'N'], true)) {
            $tipo = null;
        }
        $funcao = app(GCeuRelatorioFuncoesService::class)->getFuncao(request()->funcao_id);
        $data = app(GCeuRelatorioFuncoesService::class)->getList($igrejaId, request()->funcao_id, request()->gceu_id, $tipo);
        $data['igreja'] = Identifiable::fetchSessionIgrejaLocal()->nome;
        if($funcao == null){
            $data['titulo'] =  "Relatório de todas as funções do GCEU da Igreja: ".$data['igreja'];
        }else{
            $data['titulo'] =  "Relatório da função: $funcao->funcao do GCEU da Igreja: ".$data['igreja'];
        }
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório líderes GCEU não encontradd.');
        }
        return view('gceu.relatorio-igreja.funcoes', $data);
    }

    public function gceuRelatorioGceu()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;        
        $data = app(GCeuRelatorioGceuService::class)->getList($igrejaId);
        $data['titulo'] =  "Relatório de GCEU da Igreja: ".Identifiable::fetchSessionIgrejaLocal()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de GCEU não encontrado.');
        }
        return view('gceu.relatorio-igreja.gceu', $data);
    }

    public function gceuRelatorioAniversariantes()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $data = app(GCeuRelatorioAniversariantesService::class)->getList($igrejaId);
        $data['titulo'] =  "Relatório de Aniversariantes GCEU da Igreja: ".Identifiable::fetchSessionIgrejaLocal()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de Aniversariantes não encontrado.');
        }
        return view('gceu.relatorio-igreja.aniversariantes', $data);
    }

    ////////////////////////////GCEU DISTRITO/////////////////////////////
    public function gceuRelatorioDistritoGceu()
    {
        $distritoId = Identifiable::fetchtSessionDistrito()->id;    
        $data = app(GCeuRelatorioDistritoGceuService::class)->getList($distritoId);
        $data['titulo'] =  "Relatório de GCEU do Distrito: ".Identifiable::fetchtSessionDistrito()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de GCEU não encontrado.');
        }
        return view('gceu.relatorio-distrito.gceu', $data);
    }

    public function cartaPastoralDistrito()
    {
        $distritoId = Identifiable::fetchtSessionDistrito()->id;
        $data = app(CartaPastoralGCeuDistritoService::class)->getList($distritoId);
        if (!$data) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral-distrito.relatorio', $data);
    }


    public function cartaPastoralVisualizarHtmlDistrito($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral-distrito.visualizar', ['cartaPastoral' =>  $cartaPastoral]);
    }

    public function cartaPastoralPdfDistrito($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        //return view('gceu.carta-pastoral.visualizar', ['cartaPastoral' =>  $cartaPastoral]);

        $pdf = FacadePdf::loadView('gceu.carta-pastoral-distrito.pdf', ['cartaPastoral' =>  $cartaPastoral]);
        return $pdf->stream('carta-pastoral-'.$cartaPastoral->titulo.'.pdf');
    }

    public function gceuRelatorioDistritoFuncoes(Request $request)
    {

        $distritoId = Identifiable::fetchtSessionDistrito()->id;
        $tipo = request()->tipo;
        if (!in_array($tipo, ['M', 'C', 'V', 'N'], true)) {
            $tipo = null;
        }
        $funcao = app(GCeuRelatorioDistritoFuncoesService::class)->getFuncao(request()->funcao_id);
        $data = app(GCeuRelatorioDistritoFuncoesService::class)->getList($distritoId, request()->funcao_id, request()->gceu_id, $tipo);
        
        $data['igreja'] = Identifiable::fetchtSessionDistrito()->nome;
        if($funcao == null){
            $data['titulo'] =  "Relatório de todas as funções do GCEU do Distrito: ".$data['igreja'];
        }else{
            $data['titulo'] =  "Relatório da função: $funcao->funcao do GCEU do Distrito: ".$data['igreja'];
        }
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório funções GCEU não encontradd.');
        }
        return view('gceu.relatorio-distrito.funcoes', $data);
    }

    public function gceuRelatorioDistritoAniversariantes()
    {
        $distritoId = Identifiable::fetchtSessionDistrito()->id;
        $data = app(GCeuRelatorioDistritoAniversariantesService::class)->getList($distritoId);

        $data['titulo'] =  "Relatório de Aniversariantes GCEU do Distrito: ".Identifiable::fetchtSessionDistrito()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de Aniversariantes não encontrado.');
        }
        return view('gceu.relatorio-distrito.aniversariantes', $data);
    }
    
     ////////////////////////////GCEU REGIAO/////////////////////////////
    public function gceuRelatorioRegiaoGceu()
    {
        $regiaoId = Identifiable::fetchtSessionRegiao()->id;  
        $data = app(GCeuRelatorioRegiaoGceuService::class)->getList($regiaoId);
        $data['titulo'] =  "Relatório de GCEU da região: ".Identifiable::fetchtSessionRegiao()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de GCEU não encontrado.');
        }
        return view('gceu.relatorio-regiao.gceu', $data);
    }

    public function cartaPastoralRegiao()
    {
        $regiaoId = Identifiable::fetchtSessionRegiao()->id;
        $data = app(CartaPastoralGCeuRegiaoService::class)->getList($regiaoId);
        if (!$data) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral-regiao.relatorio', $data);
    }


    public function cartaPastoralVisualizarHtmlRegiao($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        return view('gceu.carta-pastoral-regiao.visualizar', ['cartaPastoral' =>  $cartaPastoral]);
    }

    public function cartaPastoralPdfRegiao($id)
    {
        $cartaPastoral = app(VisualizarGCeuCartaPastoralService::class)->findOne($id);
        if (!$cartaPastoral) {
            return redirect()->route('gceu.carta-pastoral')->with('error', 'Carta pastoral não encontrada.');
        }
        //return view('gceu.carta-pastoral.visualizar', ['cartaPastoral' =>  $cartaPastoral]);

        $pdf = FacadePdf::loadView('gceu.carta-pastoral-regiao.pdf', ['cartaPastoral' =>  $cartaPastoral]);
        return $pdf->stream('carta-pastoral-'.$cartaPastoral->titulo.'.pdf');
    }

    public function gceuRelatorioRegiaoFuncoes(Request $request)
    {

        $regiaoId = Identifiable::fetchtSessionRegiao()->id;
        $tipo = request()->tipo;
        if (!in_array($tipo, ['M', 'C', 'V', 'N'], true)) {
            $tipo = null;
        }
        $funcao = app(GCeuRelatorioRegiaoFuncoesService::class)->getFuncao(request()->funcao_id);
        $data = app(GCeuRelatorioRegiaoFuncoesService::class)->getList(
            $regiaoId,
            request()->distrito_id,
            request()->igreja_id,
            request()->funcao_id,
            request()->gceu_id,
            $tipo
        );
        
        $data['igreja'] = Identifiable::fetchtSessionRegiao()->nome;
        if($funcao == null){
            $data['titulo'] =  "Relatório de todas as funções do GCEU da Região: ".$data['igreja'];
        }else{
            $data['titulo'] =  "Relatório da função: $funcao->funcao do GCEU da Região: ".$data['igreja'];
        }
        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório funções GCEU não encontradd.');
        }
        return view('gceu.relatorio-regiao.funcoes', $data);
    }
    
    public function gceuRelatorioRegiaoAniversariantes()
    {
        $regiaoId = Identifiable::fetchtSessionRegiao()->id;
        $data = app(GCeuRelatorioRegiaoAniversariantesService::class)->getList($regiaoId);

        $data['titulo'] =  "Relatório de Aniversariantes GCEU da Região: ".Identifiable::fetchtSessionRegiao()->nome;

        if (!$data) {
            return redirect()->route('gceu.index')->with('error', 'Relatório de Aniversariantes não encontrado.');
        }
        return view('gceu.relatorio-regiao.aniversariantes', $data);
    }
}
