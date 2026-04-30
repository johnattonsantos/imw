<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdTurma extends Model
{
    use HasFactory;

    protected $table = 'ebd_turmas';

    protected $fillable = [
        'classe_id',
        'professor_id',
        'nome',
        'ano',
        'semestre',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function classe()
    {
        return $this->belongsTo(EbdClasse::class, 'classe_id');
    }

    public function professor()
    {
        return $this->belongsTo(EbdProfessor::class, 'professor_id');
    }

    public function alunosVinculos()
    {
        return $this->hasMany(EbdTurmaAluno::class, 'turma_id');
    }

    public function alunos()
    {
        return $this->belongsToMany(EbdAluno::class, 'ebd_turma_alunos', 'turma_id', 'aluno_id')
            ->withPivot(['id', 'data_entrada', 'data_saida', 'ativo'])
            ->withTimestamps();
    }

    public function diarios()
    {
        return $this->hasMany(EbdDiario::class, 'turma_id');
    }
}
