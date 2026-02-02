<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FinanceiroPlanoContaTipoInstituicao extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'financeiro_plano_conta_tipo_instituicao';

    protected $fillable = [
        'plano_conta_id',
        'tipo_instituicao_id',
    ];

    public function planoConta()
    {
        return $this->belongsTo(FinanceiroPlanoConta::class, 'plano_conta_id');
    }
}
