<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;


class FinanceiroGrade extends Model implements Auditable
{
    
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'financeiro_grades';

    protected $fillable = [
        'ano',
        'jan',
        'fev',
        'mar',
        'abr',
        'mai',
        'jun',
        'jul',
        'ago',
        'set',
        'out',
        'nov',
        'dez',
        'o13',
        'distrito_id',
        'igreja_id',
        'membro_id',
        'regiao_id',
    ];
}
