<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdTurmaAluno extends Model
{
    use HasFactory;

    protected $table = 'ebd_turma_alunos';

    protected $fillable = [
        'turma_id',
        'aluno_id',
        'data_entrada',
        'data_saida',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_entrada' => 'date',
        'data_saida' => 'date',
    ];

    public function turma()
    {
        return $this->belongsTo(EbdTurma::class, 'turma_id');
    }

    public function aluno()
    {
        return $this->belongsTo(EbdAluno::class, 'aluno_id');
    }
}
