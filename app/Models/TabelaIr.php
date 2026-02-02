<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class TabelaIr extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'tabelas_ir';

    protected $fillable = [
        'ano',
        'faixa',
        'deducao_faixa',
        'valor_min',
        'valor_max',
        'aliquota',
        'deducao',
    ];
}
