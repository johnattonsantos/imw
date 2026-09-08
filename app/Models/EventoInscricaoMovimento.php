<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class EventoInscricaoMovimento extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'evento_inscricao_movimentos';

    protected $fillable = [
        'evento_inscricao_id',
        'evento_id',
        'tipo',
        'registrado_por',
        'registrado_em',
        'observacoes',
    ];

    protected $casts = [
        'registrado_em' => 'datetime',
    ];

    public function inscricao()
    {
        return $this->belongsTo(EventoInscricao::class, 'evento_inscricao_id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
