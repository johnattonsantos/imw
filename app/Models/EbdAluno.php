<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdAluno extends Model
{
    use HasFactory;

    protected $table = 'ebd_alunos';

    protected $fillable = [
        'membro_id',
        'ativo',
        'observacoes',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function membro()
    {
        return $this->belongsTo(MembresiaMembro::class, 'membro_id');
    }

    public function turmaVinculos()
    {
        return $this->hasMany(EbdTurmaAluno::class, 'aluno_id');
    }
}
