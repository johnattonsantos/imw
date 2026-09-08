<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class EventoLocal extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'evento_locais';

    protected $fillable = [
        'regiao_id',
        'nome',
        'endereco',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function regiao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_id');
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'evento_local_id');
    }
}
