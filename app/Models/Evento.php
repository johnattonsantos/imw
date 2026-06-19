<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Evento extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'eventos';

    protected $fillable = [
        'instituicao_id',
        'evento_proposito_id',
        'titulo',
        'descricao',
        'local',
        'data_inicio',
        'hora_inicio',
        'data_fim',
        'hora_fim',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id');
    }

    public function proposito()
    {
        return $this->belongsTo(EventoProposito::class, 'evento_proposito_id');
    }

    public function equipe()
    {
        return $this->hasMany(EventoEquipe::class, 'evento_id')->orderByDesc('lider')->orderBy('nome');
    }

    public function lider()
    {
        return $this->hasOne(EventoEquipe::class, 'evento_id')->where('lider', true);
    }
}
