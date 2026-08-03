<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class EventoProposito extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'evento_propositos';

    protected $fillable = [
        'nome',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'evento_proposito_id');
    }
}
