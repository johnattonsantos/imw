<?php

namespace App\Http\Controllers;

use App\Models\EbdAgenda;
use App\Models\EbdAluno;
use App\Models\EbdClasse;
use App\Models\EbdDiario;
use App\Models\EbdLideranca;
use App\Models\EbdProfessor;
use App\Models\EbdTurma;
use App\Traits\Identifiable;

class EbdDashboardController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $scopeMembro = fn ($query) => $query->where('igreja_id', $igrejaId);

        return view('ebd.dashboard', [
            'totalLiderancas' => EbdLideranca::whereHas('membro', $scopeMembro)->count(),
            'totalProfessores' => EbdProfessor::whereHas('membro', $scopeMembro)->count(),
            'totalAlunos' => EbdAluno::whereHas('membro', $scopeMembro)->count(),
            'totalClasses' => EbdClasse::count(),
            'totalTurmas' => EbdTurma::whereHas('professor.membro', $scopeMembro)->count(),
            'totalDiarios' => EbdDiario::whereHas('turma.professor.membro', $scopeMembro)->count(),
            'totalAgendas' => EbdAgenda::where(function ($query) use ($scopeMembro) {
                $query->whereNull('turma_id')
                    ->orWhereHas('turma.professor.membro', $scopeMembro);
            })->count(),
        ]);
    }
}
