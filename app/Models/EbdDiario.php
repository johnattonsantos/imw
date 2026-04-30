<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdDiario extends Model
{
    use HasFactory;

    protected $table = 'ebd_diarios';

    protected $fillable = [
        'turma_id',
        'data_aula',
        'hora_inicio',
        'hora_fim',
        'periodo_aula',
        'tema_aula',
        'conteudo',
        'observacoes',
    ];

    protected $casts = [
        'data_aula' => 'date',
    ];

    public function turma()
    {
        return $this->belongsTo(EbdTurma::class, 'turma_id');
    }

    public function presencas()
    {
        return $this->hasMany(EbdDiarioPresenca::class, 'diario_id');
    }
}
