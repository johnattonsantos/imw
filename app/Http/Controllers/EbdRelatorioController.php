<?php

namespace App\Http\Controllers;

use App\Models\EbdAluno;
use App\Models\EbdClasse;
use App\Models\EbdLideranca;
use App\Models\EbdProfessor;
use App\Models\EbdTurma;
use App\Traits\Identifiable;
use Illuminate\Http\Request;

class EbdRelatorioController extends Controller
{
    use Identifiable;

    public function listaEbd(Request $request)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
        ];

        $applyAtivoFilter = function ($query) use ($filters) {
            if ($filters['ativo'] === '1') {
                $query->where('ativo', true);
            } elseif ($filters['ativo'] === '0') {
                $query->where('ativo', false);
            }
        };

        $classes = EbdClasse::where('igreja_id', $igrejaId)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($inner) use ($filters) {
                    $inner->where('nome', 'like', "%{$filters['q']}%")
                        ->orWhere('faixa_etaria', 'like', "%{$filters['q']}%")
                        ->orWhere('descricao', 'like', "%{$filters['q']}%");
                });
            })
            ->when(true, $applyAtivoFilter)
            ->orderByDesc('ativo')
            ->orderBy('nome')
            ->get();

        return view('ebd.relatorios.lista-ebd', compact('classes', 'filters'));
    }

    public function alunos(Request $request)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $scopeMembro = fn ($query) => $query->where('igreja_id', $igrejaId);

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'turma_id' => (string) $request->query('turma_id', ''),
            'status_membro' => (string) $request->query('status_membro', ''),
            'vinculo' => (string) $request->query('vinculo', ''),
        ];

        $turmasFiltro = EbdTurma::whereHas('professor.membro', $scopeMembro)
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get(['id', 'nome', 'ano']);

        $alunos = EbdAluno::with(['membro.contato'])
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
            ->when($filters['turma_id'] !== '', fn ($query) => $query->whereHas('turmaVinculos', fn ($tv) => $tv->where('turma_id', $filters['turma_id'])))
            ->when($filters['status_membro'] !== '', fn ($query) => $query->whereHas('membro', fn ($m) => $m->where('status', $filters['status_membro'])))
            ->when($filters['vinculo'] !== '', fn ($query) => $query->whereHas('membro', fn ($m) => $m->where('vinculo', $filters['vinculo'])))
            ->orderByDesc('ativo')
            ->orderByDesc('id')
            ->get();

        return view('ebd.relatorios.alunos', compact('alunos', 'filters', 'turmasFiltro'));
    }

    public function professores(Request $request)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $scopeMembro = fn ($query) => $query->where('igreja_id', $igrejaId);

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'status_membro' => (string) $request->query('status_membro', ''),
        ];

        $professores = EbdProfessor::with(['membro.contato'])
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

        return view('ebd.relatorios.professores', compact('professores', 'filters'));
    }

    public function liderancas(Request $request)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $scopeMembro = fn ($query) => $query->where('igreja_id', $igrejaId);

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'ativo' => (string) $request->query('ativo', ''),
            'cargo' => trim((string) $request->query('cargo', '')),
            'status_membro' => (string) $request->query('status_membro', ''),
        ];

        $liderancas = EbdLideranca::with(['membro.contato'])
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

        return view('ebd.relatorios.liderancas', compact('liderancas', 'filters'));
    }
}
