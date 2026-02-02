<?php

namespace App\Models;

use App\Traits\Identifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FinanceiroPlanoConta extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Identifiable, AuditableTrait;

    const TP_ENTRADA = 'E';
    const TP_SAIDA = 'S';
    const TP_TRANSFERENCIA = 'T';
    const TP_R = 'R';

    protected $table = 'financeiro_plano_contas';

    protected $fillable = [
        'nome',
        'posicao',
        'numeracao',
        'tipo',
        'conta_pai_id',
        'selecionavel',
        'essencial',
    ];

    public function lancamentos(): HasMany
    {
        return $this->hasMany(FinanceiroLancamento::class, 'plano_conta_id');
    }


    public function totalLancamentos()
    {
        return $this->lancamentos()
            ->where('conciliado', 0)
            ->sum('valor');
    }

    public function lancamentosPorIgreja()
    {
        return $this->hasMany(FinanceiroLancamento::class, 'plano_conta_id')
            ->where('instituicao_id', Identifiable::fetchSessionIgrejaLocal()->id);

    }

    public function tiposInstituicoes()
    {
        return $this->hasMany(FinanceiroPlanoContaTipoInstituicao::class, 'plano_conta_id');
    }
}
