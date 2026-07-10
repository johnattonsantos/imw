<?php

namespace App\Http\Controllers;

use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\Juridico\RegiaoAcao;
use App\Models\Juridico\RegiaoAdvogado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegiaoJuridicoController extends Controller
{
    private const STATUS = [
        'em_curso' => 'Em curso',
        'transitada_julgado' => 'Transitada em julgado',
        'encerrada' => 'Encerrada',
    ];

    private const RESULTADOS = [
        'sem_sentenca' => 'Sem sentença',
        'favor' => 'Favorável',
        'contra' => 'Contra',
        'parcial' => 'Parcial',
        'acordo' => 'Acordo',
    ];

    private const TIPOS_ADVOGADO = [
        'causa' => 'Advogado da causa',
        'oposicao' => 'Advogado da oposição',
        'ambos' => 'Ambos',
    ];

    public function indexAcoes()
    {
        $acoes = $this->acoesQuery()
            ->orderByDesc('id')
            ->paginate(15);

        $statusOptions = self::STATUS;
        $resultadoOptions = self::RESULTADOS;

        return view('regiao.juridico.acoes.index', compact('acoes', 'statusOptions', 'resultadoOptions'));
    }

    public function createAcao()
    {
        $acao = new RegiaoAcao(['status' => 'em_curso', 'resultado' => 'sem_sentenca']);
        $instituicoes = $this->instituicoesRegiao();
        $advogados = $this->advogadosOptions();
        $statusOptions = self::STATUS;
        $resultadoOptions = self::RESULTADOS;

        return view('regiao.juridico.acoes.create', compact('acao', 'instituicoes', 'advogados', 'statusOptions', 'resultadoOptions'));
    }

    public function storeAcao(Request $request)
    {
        $data = $this->validateAcao($request);
        $data['regiao_id'] = $this->regiaoId();
        $data['custo_demanda'] = $this->normalizeMoney($data['custo_demanda'] ?? null);

        RegiaoAcao::create($data);

        return redirect()->route('regiao.juridico.acoes.index')->with('success', 'Ação judicial cadastrada com sucesso.');
    }

    public function showAcao(RegiaoAcao $acao)
    {
        $this->ensureAcaoRegiao($acao);
        $acao->load(['instituicao.instituicaoPai', 'advogadoCausa', 'advogadoOposicao']);
        $statusOptions = self::STATUS;
        $resultadoOptions = self::RESULTADOS;

        return view('regiao.juridico.acoes.show', compact('acao', 'statusOptions', 'resultadoOptions'));
    }

    public function editAcao(RegiaoAcao $acao)
    {
        $this->ensureAcaoRegiao($acao);
        $instituicoes = $this->instituicoesRegiao();
        $advogados = $this->advogadosOptions();
        $statusOptions = self::STATUS;
        $resultadoOptions = self::RESULTADOS;

        return view('regiao.juridico.acoes.edit', compact('acao', 'instituicoes', 'advogados', 'statusOptions', 'resultadoOptions'));
    }

    public function updateAcao(Request $request, RegiaoAcao $acao)
    {
        $this->ensureAcaoRegiao($acao);
        $data = $this->validateAcao($request);
        $data['custo_demanda'] = $this->normalizeMoney($data['custo_demanda'] ?? null);

        $acao->update($data);

        return redirect()->route('regiao.juridico.acoes.index')->with('success', 'Ação judicial atualizada com sucesso.');
    }

    public function destroyAcao(RegiaoAcao $acao)
    {
        $this->ensureAcaoRegiao($acao);
        $acao->delete();

        return redirect()->route('regiao.juridico.acoes.index')->with('success', 'Ação judicial excluída com sucesso.');
    }

    public function indexAdvogados()
    {
        $advogados = RegiaoAdvogado::query()
            ->where('regiao_id', $this->regiaoId())
            ->orderBy('nome')
            ->paginate(15);

        $tipoOptions = self::TIPOS_ADVOGADO;

        return view('regiao.juridico.advogados.index', compact('advogados', 'tipoOptions'));
    }

    public function createAdvogado()
    {
        $advogado = new RegiaoAdvogado(['tipo' => 'causa']);
        $tipoOptions = self::TIPOS_ADVOGADO;

        return view('regiao.juridico.advogados.create', compact('advogado', 'tipoOptions'));
    }

    public function storeAdvogado(Request $request)
    {
        $data = $this->validateAdvogado($request);
        $data['regiao_id'] = $this->regiaoId();

        RegiaoAdvogado::create($data);

        return redirect()->route('regiao.juridico.advogados.index')->with('success', 'Advogado cadastrado com sucesso.');
    }

    public function editAdvogado(RegiaoAdvogado $advogado)
    {
        $this->ensureAdvogadoRegiao($advogado);
        $tipoOptions = self::TIPOS_ADVOGADO;

        return view('regiao.juridico.advogados.edit', compact('advogado', 'tipoOptions'));
    }

    public function updateAdvogado(Request $request, RegiaoAdvogado $advogado)
    {
        $this->ensureAdvogadoRegiao($advogado);
        $advogado->update($this->validateAdvogado($request));

        return redirect()->route('regiao.juridico.advogados.index')->with('success', 'Advogado atualizado com sucesso.');
    }

    public function destroyAdvogado(RegiaoAdvogado $advogado)
    {
        $this->ensureAdvogadoRegiao($advogado);
        $advogado->delete();

        return redirect()->route('regiao.juridico.advogados.index')->with('success', 'Advogado excluído com sucesso.');
    }

    public function relatorios(Request $request)
    {
        $acoes = $this->acoesQuery()
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('resultado'), fn ($query) => $query->where('resultado', $request->input('resultado')))
            ->when($request->filled('instituicao_id'), fn ($query) => $query->where('instituicao_id', (int) $request->input('instituicao_id')))
            ->when($request->filled('advogado_id'), function ($query) use ($request) {
                $advogadoId = (int) $request->input('advogado_id');
                $query->where(function ($sub) use ($advogadoId) {
                    $sub->where('advogado_causa_id', $advogadoId)
                        ->orWhere('advogado_oposicao_id', $advogadoId);
                });
            })
            ->orderBy('status')
            ->orderByDesc('data_sentenca')
            ->orderByDesc('id')
            ->get();

        $resumo = (object) [
            'em_curso' => $acoes->where('status', 'em_curso')->count(),
            'transitada_julgado' => $acoes->where('status', 'transitada_julgado')->count(),
            'favor' => $acoes->where('resultado', 'favor')->count(),
            'contra' => $acoes->where('resultado', 'contra')->count(),
            'total' => $acoes->count(),
            'custo_total' => $acoes->sum(fn ($acao) => (float) $acao->custo_demanda),
        ];

        $instituicoes = $this->instituicoesRegiao();
        $advogados = $this->advogadosOptions();
        $statusOptions = self::STATUS;
        $resultadoOptions = self::RESULTADOS;

        return view('regiao.juridico.relatorios.index', compact(
            'acoes',
            'resumo',
            'instituicoes',
            'advogados',
            'statusOptions',
            'resultadoOptions'
        ));
    }

    private function acoesQuery()
    {
        return RegiaoAcao::query()
            ->where('regiao_id', $this->regiaoId())
            ->with(['instituicao.instituicaoPai', 'advogadoCausa', 'advogadoOposicao']);
    }

    private function validateAcao(Request $request): array
    {
        $allowedInstituicoes = $this->instituicoesRegiao()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedAdvogados = $this->advogadosOptions()->pluck('id')->map(fn ($id) => (int) $id)->all();

        return $request->validate([
            'instituicao_id' => ['required', 'integer', Rule::in($allowedInstituicoes)],
            'advogado_causa_id' => ['nullable', 'integer', Rule::in($allowedAdvogados)],
            'advogado_oposicao_id' => ['nullable', 'integer', Rule::in($allowedAdvogados)],
            'numero_processo' => ['nullable', 'string', 'max:120'],
            'autor' => ['required', 'string', 'max:180'],
            'reu' => ['required', 'string', 'max:180'],
            'vara_tribunal' => ['nullable', 'string', 'max:180'],
            'advogado_oposicao_nome' => ['nullable', 'string', 'max:180'],
            'status' => ['required', Rule::in(array_keys(self::STATUS))],
            'resultado' => ['required', Rule::in(array_keys(self::RESULTADOS))],
            'data_distribuicao' => ['nullable', 'date'],
            'data_sentenca' => ['nullable', 'date'],
            'custo_demanda' => ['nullable', 'string', 'max:30'],
            'objeto' => ['nullable', 'string'],
            'teor_decisao' => ['nullable', 'string'],
            'outros' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'instituicao_id.required' => 'Selecione a instituição.',
            'instituicao_id.in' => 'A instituição selecionada não pertence à região logada.',
            'autor.required' => 'Informe quem é o autor.',
            'reu.required' => 'Informe quem é a ré.',
        ]);
    }

    private function validateAdvogado(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:180'],
            'tipo' => ['required', Rule::in(array_keys(self::TIPOS_ADVOGADO))],
            'registro_oab' => ['nullable', 'string', 'max:60'],
            'telefone' => ['nullable', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:150'],
            'contatos' => ['nullable', 'string', 'max:255'],
            'endereco_escritorio' => ['nullable', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'nome.required' => 'Informe o nome do advogado.',
            'email.email' => 'Informe um e-mail válido.',
        ]);
    }

    private function instituicoesRegiao()
    {
        $regiaoId = $this->regiaoId();

        $distritoIds = InstituicoesInstituicao::query()
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where(function ($query) use ($regiaoId) {
                $query->where('instituicao_pai_id', $regiaoId)
                    ->orWhere('regiao_id', $regiaoId);
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $igrejaIds = InstituicoesInstituicao::query()
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where(function ($query) use ($regiaoId, $distritoIds) {
                $query->where('regiao_id', $regiaoId)
                    ->when(!empty($distritoIds), fn ($sub) => $sub->orWhereIn('instituicao_pai_id', $distritoIds));
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return InstituicoesInstituicao::query()
            ->where('ativo', 1)
            ->where(function ($query) use ($regiaoId, $distritoIds, $igrejaIds) {
                $query->where('id', $regiaoId)
                    ->when(!empty($distritoIds), fn ($sub) => $sub->orWhereIn('id', $distritoIds))
                    ->when(!empty($igrejaIds), fn ($sub) => $sub->orWhereIn('id', $igrejaIds))
                    ->when(!empty($igrejaIds), function ($sub) use ($igrejaIds) {
                        $sub->orWhere(function ($inner) use ($igrejaIds) {
                            $inner->where('tipo_instituicao_id', InstituicoesTipoInstituicao::CONGREGACAO)
                                ->whereIn('instituicao_pai_id', $igrejaIds);
                        });
                    });
            })
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$regiaoId])
            ->orderBy('tipo_instituicao_id')
            ->orderBy('nome')
            ->get(['id', 'nome', 'tipo_instituicao_id', 'instituicao_pai_id']);
    }

    private function advogadosOptions()
    {
        return RegiaoAdvogado::query()
            ->where('regiao_id', $this->regiaoId())
            ->orderBy('nome')
            ->get(['id', 'nome', 'tipo', 'registro_oab']);
    }

    private function regiaoId(): int
    {
        $sessionPerfil = session('session_perfil');
        $regiaoId = (int) data_get($sessionPerfil, 'instituicoes.regiao.id', 0);

        if ($regiaoId > 0) {
            return $regiaoId;
        }

        $instituicaoId = (int) data_get($sessionPerfil, 'instituicao_id', 0);
        abort_if($instituicaoId <= 0, 403, 'Instituição não encontrada na sessão.');

        $currentId = $instituicaoId;
        $maxDepth = 10;

        while ($currentId > 0 && $maxDepth-- > 0) {
            $instituicao = InstituicoesInstituicao::query()
                ->select(['id', 'tipo_instituicao_id', 'instituicao_pai_id', 'regiao_id'])
                ->find($currentId);

            abort_if(!$instituicao, 403, 'Instituição não encontrada.');

            if ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::REGIAO) {
                return (int) $instituicao->id;
            }

            if (!empty($instituicao->regiao_id)) {
                return (int) $instituicao->regiao_id;
            }

            $currentId = (int) ($instituicao->instituicao_pai_id ?? 0);
        }

        abort(403, 'Perfil logado não está vinculado a uma região.');
    }

    private function ensureAcaoRegiao(RegiaoAcao $acao): void
    {
        abort_if((int) $acao->regiao_id !== $this->regiaoId(), 403);
    }

    private function ensureAdvogadoRegiao(RegiaoAdvogado $advogado): void
    {
        abort_if((int) $advogado->regiao_id !== $this->regiaoId(), 403);
    }

    private function normalizeMoney(?string $value): ?float
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = str_replace(['R$', ' ', '.'], '', $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}
