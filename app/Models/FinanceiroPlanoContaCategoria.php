<?php

namespace App\Models;

use App\Traits\Identifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FinanceiroPlanoContaCategoria extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'financeiro_plano_contas_categoria';

    protected $fillable = [
        'nome',
    ];
}
