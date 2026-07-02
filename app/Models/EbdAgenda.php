<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdAgenda extends Model
{
    use HasFactory;

    protected $table = 'ebd_agendas';

    protected $fillable = [
        'titulo',
        'descricao',
        'data_inicio',
        'data_fim',
        'turma_id',
        'local',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];

    public function turma()
    {
        return $this->belongsTo(EbdTurma::class, 'turma_id');
    }
}
