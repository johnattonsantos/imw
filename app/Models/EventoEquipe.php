<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class EventoEquipe extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'evento_equipes';

    protected $fillable = [
        'evento_id',
        'evento_funcao_id',
        'nome',
        'funcao',
        'contato',
        'lider',
    ];

    protected $casts = [
        'lider' => 'boolean',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function eventoFuncao()
    {
        return $this->belongsTo(EventoFuncao::class, 'evento_funcao_id');
    }
}
