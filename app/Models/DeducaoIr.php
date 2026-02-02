<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DeducaoIr extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'deducoes_ir';

    protected $fillable = [
        'ano',
        'tipo',
        'valor',
        'simplificado',
    ];

    protected $casts = [
        'ano'          => 'integer',
        'simplificado' => 'boolean',
        'valor'        => 'float',
    ];
}
