<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class GCeuReuniaoPessoa extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    public const TIPO_VISITANTE = 'V';
    public const TIPO_NOVO_CONVERTIDO = 'N';

    protected $table = 'gceu_reuniao_pessoas';

    protected $guarded = [];
}

