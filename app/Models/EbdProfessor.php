<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdProfessor extends Model
{
    use HasFactory;

    protected $table = 'ebd_professores';

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

    public function turmas()
    {
        return $this->hasMany(EbdTurma::class, 'professor_id');
    }
}
