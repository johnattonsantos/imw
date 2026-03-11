<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaRolPermanenteRecadastramento extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Compoships, AuditableTrait;

    const STATUS_RECEBIMENTO = 'A';
    const STATUS_EXCLUSAO = 'I';
    const STATUS_TRANSFERENCIA = 'T';

    protected $table = 'membresia_rolpermanente_migracao';

    protected $fillable = [
        'lastrec',
        'status',
        'numero_rol',
        'codigo_host',
        'dt_recepcao',
        'dt_exclusao',
        'clerigo_id',
        'distrito_id',
        'igreja_id',
        'membro_id',
        'modo_exclusao_id',
        'modo_recepcao_id',
        'regiao_id',
        'congregacao_id'
    ];

    protected $casts = [
        'dt_recepcao' => 'date',
        'dt_exclusao' => 'date',
    ];
}
