<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class TipoArquivo extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'tipo_arquivo';

    protected $fillable = [
        'extensao',
    ];
}
