<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FinanceiroSaldoConsolidadoMensal extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'financeiro_saldo_consolidado_mensal';

    protected $fillable = [
        'data_hora',
        'total_entradas',
        'total_saidas',
        'saldo_anterior',
        'saldo_final',
        'estorno',
        'auditado',
        'caixa_id',
        'instituicao_id',
        'ano',
        'mes',
        'total_transf_entradas',
        'total_transf_saidas',
        'aux_data_hora',
        'aux_saldo_anterior',
        'aux_saldo_final',
        'aux_total_entradas',
        'aux_total_saidas',
        'aux_total_transf_entradas',
        'aux_total_transf_saidas'
    ];

    public function caixa()
    {
        return $this->belongsTo(FinanceiroCaixa::class, 'caixa_id');
    }

    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id');
    }
}
