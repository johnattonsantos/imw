<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class EventoFuncao extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'evento_funcoes';

    protected $fillable = [
        'nome',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function equipes()
    {
        return $this->hasMany(EventoEquipe::class, 'evento_funcao_id');
    }
}
