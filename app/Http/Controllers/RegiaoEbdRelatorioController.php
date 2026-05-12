<?php

namespace App\Http\Controllers;

use App\Models\EbdAluno;
use App\Models\EbdAgenda;
use App\Models\EbdClasse;
use App\Models\EbdDiario;
use App\Models\EbdLideranca;
use App\Models\EbdProfessor;
use App\Models\EbdTurma;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegiaoEbdRelatorioController extends Controller
{
    use Identifiable;

    public function lista(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $classes = EbdClasse::query()
            ->join('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'ebd_classes.igreja_id')
            ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->where('igreja.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->whereNull('igreja.data_encerramento')
            ->whereIn('ebd_classes.igreja_id', $allowedIgrejasIds)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner->where('ebd_classes.nome', 'like', "%{$filters['q']}%")
                        ->orWhere('ebd_classes.faixa_etaria', 'like', "%{$filters['q']}%")
                        ->orWhere('ebd_classes.descricao', 'like', "%{$filters['q']}%")
                        ->orWhere('igreja.nome', 'like', "%{$filters['q']}%")
                        ->orWhere('distrito.nome', 'like', "%{$filters['q']}%");
                });
            })
            ->when($filters['ativo'] === '1', fn ($query) => $query->where('ebd_classes.ativo', true))
            ->when($filters['ativo'] === '0', fn ($query) => $query->where('ebd_classes.ativo', false))
            ->select([
                'ebd_classes.id',
                'ebd_classes.nome',
                'ebd_classes.faixa_etaria',
                'ebd_classes.descricao',
                'ebd_classes.ativo',
                'igreja.nome as igreja_nome',
                'distrito.nome as distrito_nome',
            ])
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->orderBy('ebd_classes.nome')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.lista', compact(
            'classes',
            'distritos',
            'igrejas',
            'filters'
        ));
    }

    public function alunos(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'status_membro' => (string) $request->query('status_membro', ''),
            'vinculo' => (string) $request->query('vinculo', ''),
            'turma_id' => (string) $request->query('turma_id', ''),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $scopeMembro = fn ($query) => $query->whereIn('igreja_id', $allowedIgrejasIds);

        $turmasFiltro = EbdTurma::with('classe')
            ->whereHas('professor.membro', $scopeMembro)
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get(['id', 'nome', 'ano', 'classe_id']);

        $alunos = EbdAluno::with(['membro.contato', 'membro.distrito', 'membro.igreja'])
            ->withCount(['turmaVinculos as total_turmas_ativas' => fn ($q) => $q->where('ativo', true)])
            ->whereHas('membro', $scopeMembro)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner
                        ->whereHas('membro', function ($m) use ($filters) {
                            $m->where('nome', 'like', "%{$filters['q']}%")
                                ->orWhere('cpf', 'like', "%{$filters['q']}%");
                        })
                        ->orWhereHas('membro.contato', function ($c) use ($filters) {
                            $c->where('telefone_preferencial', 'like', "%{$filters['q']}%")
                                ->orWhere('telefone_alternativo', 'like', "%{$filters['q']}%")
                                ->orWhere('telefone_whatsapp', 'like', "%{$filters['q']}%")
                                ->orWhere('email_preferencial', 'like', "%{$filters['q']}%")
                                ->orWhere('email_alternativo', 'like', "%{$filters['q']}%");
                        });
                });
            })
            ->when($filters['ativo'] === '1', fn ($query) => $query->where('ativo', true))
            ->when($filters['ativo'] === '0', fn ($query) => $query->where('ativo', false))
            ->when($filters['status_membro'] !== '', fn ($query) => $query->whereHas('membro', fn ($m) => $m->where('status', $filters['status_membro'])))
            ->when($filters['vinculo'] !== '', fn ($query) => $query->whereHas('membro', fn ($m) => $m->where('vinculo', $filters['vinculo'])))
            ->when($filters['turma_id'] !== '', fn ($query) => $query->whereHas('turmaVinculos', fn ($tv) => $tv->where('turma_id', $filters['turma_id'])))
            ->orderByDesc('ativo')
            ->orderByDesc('id')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.alunos', compact(
            'alunos',
            'distritos',
            'igrejas',
            'turmasFiltro',
            'filters'
        ));
    }

    public function professores(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'status_membro' => (string) $request->query('status_membro', ''),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $scopeMembro = fn ($query) => $query->whereIn('igreja_id', $allowedIgrejasIds);

        $professores = EbdProfessor::with(['membro.contato', 'membro.distrito', 'membro.igreja'])
            ->withCount(['turmas as total_turmas_ativas' => fn ($q) => $q->where('ativo', true)])
            ->whereHas('membro', $scopeMembro)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner
                        ->whereHas('membro', function ($m) use ($filters) {
                            $m->where('nome', 'like', "%{$filters['q']}%")
                                ->orWhere('cpf', 'like', "%{$filters['q']}%");
                        })
                        ->orWhereHas('membro.contato', function ($c) use ($filters) {
                            $c->where('telefone_preferencial', 'like', "%{$filters['q']}%")
                                ->orWhere('telefone_alternativo', 'like', "%{$filters['q']}%")
                                ->orWhere('telefone_whatsapp', 'like', "%{$filters['q']}%")
                                ->orWhere('email_preferencial', 'like', "%{$filters['q']}%")
                                ->orWhere('email_alternativo', 'like', "%{$filters['q']}%");
                        });
                });
            })
            ->when($filters['ativo'] === '1', fn ($query) => $query->where('ativo', true))
            ->when($filters['ativo'] === '0', fn ($query) => $query->where('ativo', false))
            ->when($filters['status_membro'] !== '', fn ($query) => $query->whereHas('membro', fn ($m) => $m->where('status', $filters['status_membro'])))
            ->orderByDesc('ativo')
            ->orderByDesc('id')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.professores', compact(
            'professores',
            'distritos',
            'igrejas',
            'filters'
        ));
    }

    public function liderancas(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'cargo' => trim((string) $request->query('cargo', '')),
            'status_membro' => (string) $request->query('status_membro', ''),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $scopeMembro = fn ($query) => $query->whereIn('igreja_id', $allowedIgrejasIds);

        $liderancas = EbdLideranca::with(['membro.contato', 'membro.distrito', 'membro.igreja'])
            ->whereHas('membro', $scopeMembro)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner
                        ->whereHas('membro', function ($m) use ($filters) {
                            $m->where('nome', 'like', "%{$filters['q']}%")
                                ->orWhere('cpf', 'like', "%{$filters['q']}%");
                        })
                        ->orWhere('cargo', 'like', "%{$filters['q']}%")
                        ->orWhereHas('membro.contato', function ($c) use ($filters) {
                            $c->where('telefone_preferencial', 'like', "%{$filters['q']}%")
                                ->orWhere('telefone_alternativo', 'like', "%{$filters['q']}%")
                                ->orWhere('telefone_whatsapp', 'like', "%{$filters['q']}%")
                                ->orWhere('email_preferencial', 'like', "%{$filters['q']}%")
                                ->orWhere('email_alternativo', 'like', "%{$filters['q']}%");
                        });
                });
            })
            ->when($filters['ativo'] === '1', fn ($query) => $query->where('ativo', true))
            ->when($filters['ativo'] === '0', fn ($query) => $query->where('ativo', false))
            ->when($filters['cargo'] !== '', fn ($query) => $query->where('cargo', 'like', "%{$filters['cargo']}%"))
            ->when($filters['status_membro'] !== '', fn ($query) => $query->whereHas('membro', fn ($m) => $m->where('status', $filters['status_membro'])))
            ->orderByDesc('ativo')
            ->orderBy('cargo')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.liderancas', compact(
            'liderancas',
            'distritos',
            'igrejas',
            'filters'
        ));
    }

    public function classes(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $classes = EbdClasse::query()
            ->join('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'ebd_classes.igreja_id')
            ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->withCount(['turmas as total_turmas_ativas' => fn ($q) => $q->where('ativo', true)])
            ->whereIn('igreja_id', $allowedIgrejasIds)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner->where('ebd_classes.nome', 'like', "%{$filters['q']}%")
                        ->orWhere('ebd_classes.faixa_etaria', 'like', "%{$filters['q']}%")
                        ->orWhere('ebd_classes.descricao', 'like', "%{$filters['q']}%")
                        ->orWhere('igreja.nome', 'like', "%{$filters['q']}%")
                        ->orWhere('distrito.nome', 'like', "%{$filters['q']}%");
                });
            })
            ->when($filters['ativo'] === '1', fn ($query) => $query->where('ebd_classes.ativo', true))
            ->when($filters['ativo'] === '0', fn ($query) => $query->where('ebd_classes.ativo', false))
            ->select([
                'ebd_classes.*',
                'igreja.nome as igreja_nome',
                'distrito.nome as distrito_nome',
            ])
            ->orderByDesc('ativo')
            ->orderBy('ebd_classes.nome')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.classes', compact(
            'classes',
            'distritos',
            'igrejas',
            'filters'
        ));
    }

    public function turmas(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'ano' => trim((string) $request->query('ano', '')),
            'semestre' => trim((string) $request->query('semestre', '')),
            'classe_id' => (string) $request->query('classe_id', ''),
            'professor_id' => (string) $request->query('professor_id', ''),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $scopeMembro = fn ($query) => $query->whereIn('igreja_id', $allowedIgrejasIds);

        $classesFiltro = EbdClasse::whereIn('igreja_id', $allowedIgrejasIds)
            ->orderBy('nome')
            ->get(['id', 'nome']);
        $professoresFiltro = EbdProfessor::query()
            ->with('membro')
            ->where('ativo', true)
            ->whereHas('membro', $scopeMembro)
            ->orderByDesc('id')
            ->get();
        $anosFiltro = EbdTurma::query()
            ->whereHas('professor.membro', $scopeMembro)
            ->select('ano')
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano');

        $turmas = EbdTurma::query()
            ->with(['classe', 'professor.membro.distrito', 'professor.membro.igreja'])
            ->withCount(['alunosVinculos as total_alunos_ativos' => fn ($q) => $q->where('ativo', true)])
            ->whereHas('professor.membro', $scopeMembro)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where('nome', 'like', "%{$filters['q']}%");
            })
            ->when($filters['classe_id'] !== '', fn ($query) => $query->where('classe_id', $filters['classe_id']))
            ->when($filters['professor_id'] !== '', fn ($query) => $query->where('professor_id', $filters['professor_id']))
            ->when($filters['ano'] !== '', fn ($query) => $query->where('ano', $filters['ano']))
            ->when($filters['semestre'] !== '', fn ($query) => $query->where('semestre', $filters['semestre']))
            ->when($filters['ativo'] === '1', fn ($query) => $query->where('ativo', true))
            ->when($filters['ativo'] === '0', fn ($query) => $query->where('ativo', false))
            ->orderByDesc('ativo')
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.turmas', compact(
            'turmas',
            'classesFiltro',
            'professoresFiltro',
            'anosFiltro',
            'distritos',
            'igrejas',
            'filters'
        ));
    }

    public function diarios(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'turma_id' => (string) $request->query('turma_id', ''),
            'periodo_aula' => trim((string) $request->query('periodo_aula', '')),
            'data_inicio' => trim((string) $request->query('data_inicio', '')),
            'data_fim' => trim((string) $request->query('data_fim', '')),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $scopeMembro = fn ($query) => $query->whereIn('igreja_id', $allowedIgrejasIds);

        $turmasFiltro = EbdTurma::whereHas('professor.membro', $scopeMembro)
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get(['id', 'nome', 'ano']);

        $diarios = EbdDiario::query()
            ->with(['turma.classe', 'turma.professor.membro.distrito', 'turma.professor.membro.igreja'])
            ->withCount('presencas')
            ->withCount(['presencas as total_presentes' => fn ($q) => $q->where('presente', true)])
            ->whereHas('turma.professor.membro', $scopeMembro)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner->where('tema_aula', 'like', "%{$filters['q']}%")
                        ->orWhere('conteudo', 'like', "%{$filters['q']}%")
                        ->orWhere('observacoes', 'like', "%{$filters['q']}%")
                        ->orWhereHas('turma', fn ($t) => $t->where('nome', 'like', "%{$filters['q']}%"))
                        ->orWhereHas('turma.classe', fn ($c) => $c->where('nome', 'like', "%{$filters['q']}%"))
                        ->orWhereHas('turma.professor.membro', fn ($m) => $m->where('nome', 'like', "%{$filters['q']}%"));
                });
            })
            ->when($filters['turma_id'] !== '', fn ($query) => $query->where('turma_id', $filters['turma_id']))
            ->when($filters['periodo_aula'] !== '', fn ($query) => $query->where('periodo_aula', $filters['periodo_aula']))
            ->when($filters['data_inicio'] !== '', fn ($query) => $query->whereDate('data_aula', '>=', $filters['data_inicio']))
            ->when($filters['data_fim'] !== '', fn ($query) => $query->whereDate('data_aula', '<=', $filters['data_fim']))
            ->orderByDesc('data_aula')
            ->orderByDesc('id')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.diarios', compact(
            'diarios',
            'turmasFiltro',
            'distritos',
            'igrejas',
            'filters'
        ));
    }

    public function agendas(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'turma_id' => (string) $request->query('turma_id', ''),
            'data_inicio' => trim((string) $request->query('data_inicio', '')),
            'data_fim' => trim((string) $request->query('data_fim', '')),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $scopeMembro = fn ($query) => $query->whereIn('igreja_id', $allowedIgrejasIds);

        $turmasFiltro = EbdTurma::whereHas('professor.membro', $scopeMembro)
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get(['id', 'nome', 'ano']);

        $agendas = EbdAgenda::query()
            ->with(['turma.classe', 'turma.professor.membro.distrito', 'turma.professor.membro.igreja'])
            ->where(function ($query) use ($scopeMembro) {
                $query->whereNull('turma_id')
                    ->orWhereHas('turma.professor.membro', $scopeMembro);
            })
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner->where('titulo', 'like', "%{$filters['q']}%")
                        ->orWhere('descricao', 'like', "%{$filters['q']}%")
                        ->orWhere('local', 'like', "%{$filters['q']}%")
                        ->orWhereHas('turma', fn ($t) => $t->where('nome', 'like', "%{$filters['q']}%"))
                        ->orWhereHas('turma.classe', fn ($c) => $c->where('nome', 'like', "%{$filters['q']}%"))
                        ->orWhereHas('turma.professor.membro', fn ($m) => $m->where('nome', 'like', "%{$filters['q']}%"));
                });
            })
            ->when($filters['turma_id'] !== '', fn ($query) => $query->where('turma_id', $filters['turma_id']))
            ->when($filters['data_inicio'] !== '', fn ($query) => $query->whereDate('data_inicio', '>=', $filters['data_inicio']))
            ->when($filters['data_fim'] !== '', fn ($query) => $query->whereDate('data_inicio', '<=', $filters['data_fim']))
            ->orderByDesc('data_inicio')
            ->orderByDesc('id')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.agendas', compact(
            'agendas',
            'turmasFiltro',
            'distritos',
            'igrejas',
            'filters'
        ));
    }

    public function geral(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'turma_id' => (string) $request->query('turma_id', ''),
            'classe_id' => (string) $request->query('classe_id', ''),
            'tipo_unidade' => trim((string) $request->query('tipo_unidade', '')),
            'presenca_status' => trim((string) $request->query('presenca_status', '')),
            'data_inicio' => trim((string) $request->query('data_inicio', '')),
            'data_fim' => trim((string) $request->query('data_fim', '')),
            'distrito_id' => (int) $request->query('distrito_id', 0),
            'igreja_id' => (int) $request->query('igreja_id', 0),
        ];

        [
            'distritos' => $distritos,
            'igrejas' => $igrejas,
            'selectedDistritoId' => $selectedDistritoId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'allowedIgrejasIds' => $allowedIgrejasIds,
        ] = $this->resolveRegionalFilters($filters);

        $turmasFiltro = EbdTurma::query()
            ->whereHas('classe', fn ($q) => $q->whereIn('igreja_id', $allowedIgrejasIds))
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get(['id', 'nome', 'ano']);

        $classesFiltro = EbdClasse::query()
            ->whereIn('igreja_id', $allowedIgrejasIds)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $registros = DB::table('ebd_turma_alunos as ta')
            ->join('ebd_turmas as t', 't.id', '=', 'ta.turma_id')
            ->join('ebd_classes as cl', 'cl.id', '=', 't.classe_id')
            ->join('instituicoes_instituicoes as i', 'i.id', '=', 'cl.igreja_id')
            ->leftJoin('instituicoes_instituicoes as d', 'd.id', '=', 'i.instituicao_pai_id')
            ->leftJoin('congregacoes_congregacoes as c', 'c.id', '=', 't.congregacao_id')
            ->join('ebd_professores as p', 'p.id', '=', 't.professor_id')
            ->join('membresia_membros as mp', 'mp.id', '=', 'p.membro_id')
            ->join('ebd_alunos as a', 'a.id', '=', 'ta.aluno_id')
            ->join('membresia_membros as ma', 'ma.id', '=', 'a.membro_id')
            ->leftJoin('ebd_diarios as di', 'di.turma_id', '=', 't.id')
            ->leftJoin('ebd_diario_presencas as dp', function ($join) {
                $join->on('dp.diario_id', '=', 'di.id')
                    ->on('dp.aluno_id', '=', 'a.id');
            })
            ->whereIn('cl.igreja_id', $allowedIgrejasIds)
            ->whereNull('i.deleted_at')
            ->where(function ($q) {
                $q->whereNull('d.id')
                    ->orWhereNull('d.deleted_at');
            })
            ->where(function ($q) {
                $q->whereNull('c.id')
                    ->orWhereNull('c.deleted_at');
            })
            ->whereNull('ma.deleted_at')
            ->whereNull('mp.deleted_at')
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner->where('cl.nome', 'like', "%{$filters['q']}%")
                        ->orWhere('t.nome', 'like', "%{$filters['q']}%")
                        ->orWhere('mp.nome', 'like', "%{$filters['q']}%")
                        ->orWhere('ma.nome', 'like', "%{$filters['q']}%")
                        ->orWhere('di.tema_aula', 'like', "%{$filters['q']}%");
                });
            })
            ->when($filters['turma_id'] !== '', fn ($query) => $query->where('t.id', $filters['turma_id']))
            ->when($filters['classe_id'] !== '', fn ($query) => $query->where('cl.id', $filters['classe_id']))
            ->when($filters['tipo_unidade'] === 'SEDE', fn ($query) => $query->whereNull('t.congregacao_id'))
            ->when($filters['tipo_unidade'] === 'CONGREGACAO', fn ($query) => $query->whereNotNull('t.congregacao_id'))
            ->when($filters['presenca_status'] === 'PRESENTE', fn ($query) => $query->where('dp.presente', 1))
            ->when($filters['presenca_status'] === 'AUSENTE', fn ($query) => $query->where('dp.presente', 0))
            ->when($filters['presenca_status'] === 'NAO_LANCADA', fn ($query) => $query->whereNull('dp.id'))
            ->when($filters['data_inicio'] !== '', fn ($query) => $query->whereDate('di.data_aula', '>=', $filters['data_inicio']))
            ->when($filters['data_fim'] !== '', fn ($query) => $query->whereDate('di.data_aula', '<=', $filters['data_fim']))
            ->select([
                'd.nome as distrito_nome',
                'i.nome as igreja_nome',
                'c.nome as congregacao_nome',
                'cl.nome as sala_nome',
                'cl.faixa_etaria as sala_faixa_etaria',
                't.nome as turma_nome',
                't.ano as turma_ano',
                't.semestre as turma_semestre',
                'mp.nome as professor_nome',
                'ma.nome as aluno_nome',
                'ma.cpf as aluno_cpf',
                'di.data_aula',
                'di.periodo_aula',
                'di.tema_aula',
                'dp.justificativa as presenca_justificativa',
                DB::raw("CASE WHEN t.congregacao_id IS NULL THEN 'SEDE' ELSE 'CONGREGACAO' END as tipo_unidade"),
                DB::raw("CASE WHEN dp.id IS NULL THEN 'NAO LANCADA' WHEN dp.presente = 1 THEN 'PRESENTE' ELSE 'AUSENTE' END as presenca_status"),
            ])
            ->orderBy('d.nome')
            ->orderBy('i.nome')
            ->orderBy('cl.nome')
            ->orderByDesc('t.ano')
            ->orderBy('t.nome')
            ->orderBy('ma.nome')
            ->orderByDesc('di.data_aula')
            ->get();

        $filters['distrito_id'] = $selectedDistritoId ?? '';
        $filters['igreja_id'] = $selectedIgrejaId ?? '';

        return view('regiao.relatorios.ebd.geral', compact(
            'registros',
            'distritos',
            'igrejas',
            'filters',
            'turmasFiltro',
            'classesFiltro'
        ));
    }

    private function resolveRegionalFilters(array $filters): array
    {
        $regiaoId = Identifiable::fetchtSessionRegiao()->id;
        $distritos = Identifiable::fetchDistritosByRegiao($regiaoId);
        $distritosIds = $distritos->pluck('id')->map(fn ($id) => (int) $id)->all();

        $selectedDistritoId = in_array((int) ($filters['distrito_id'] ?? 0), $distritosIds, true)
            ? (int) $filters['distrito_id']
            : null;

        $igrejasQuery = InstituicoesInstituicao::query()
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->whereIn('instituicao_pai_id', $distritosIds)
            ->whereNull('data_encerramento')
            ->orderBy('nome');

        if ($selectedDistritoId !== null) {
            $igrejasQuery->where('instituicao_pai_id', $selectedDistritoId);
        }

        $igrejas = $igrejasQuery->get(['id', 'nome', 'instituicao_pai_id']);
        $igrejasIds = $igrejas->pluck('id')->map(fn ($id) => (int) $id)->all();

        $selectedIgrejaId = in_array((int) ($filters['igreja_id'] ?? 0), $igrejasIds, true)
            ? (int) $filters['igreja_id']
            : null;

        $allowedIgrejasIds = $selectedIgrejaId !== null ? [$selectedIgrejaId] : $igrejasIds;

        return compact(
            'distritos',
            'distritosIds',
            'selectedDistritoId',
            'igrejas',
            'igrejasIds',
            'selectedIgrejaId',
            'allowedIgrejasIds'
        );
    }
}
