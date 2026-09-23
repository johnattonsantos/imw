<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaRolPermanenteRecadastramento extends Model implements Auditable
{
    use HasFactory, Compoships, AuditableTrait;

    const STATUS_RECEBIMENTO = 'A';
    const STATUS_EXCLUSAO = 'I';
    const STATUS_TRANSFERENCIA = 'T';

    protected $table = 'vw_rol_membros_recadastro';

    protected $primaryKey = 'membro_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

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
