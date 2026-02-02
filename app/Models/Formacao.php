<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Formacao extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'formacoes';

    protected $fillable = [
        'nivel',
    ];
}
