<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Comunicacao extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'comunicacao';

    protected $fillable = [
        'instituicao_id',
        'titulo',
        'comentario',
        'arquivo',
    ];

    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id');
    }
}
