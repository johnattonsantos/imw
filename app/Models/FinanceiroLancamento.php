<?php

namespace App\Models;

use App\Traits\Identifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FinanceiroLancamento extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    const TP_LANCAMENTO_ENTRADA = 'E';
    const TP_LANCAMENTO_SAIDA = 'S';
    
    protected $table = 'financeiro_lancamentos';

    protected $fillable = [
        'data_lancamento',
        'tipo_pagante_favorecido_id',
        'membro_id',
        'fornecedores_id',
        'clerigo_id',
        'valor',
        'pagante_favorecido',
        'descricao',
        'tipo_lancamento',
        'conciliado',
        'data_vencimento',
        'plano_conta_id',
        'data_conciliacao',
        'data_movimento',
        'caixa_id',
        'lancamento_pai_id',
        'estornado',
        'instituicao_destino_origem_id',
        'hist_distrito_id',
        'hist_geral_id',
        'hist_igreja_id',
        'hist_orgao_id',
        'hist_regiao_id',
        'hist_secretaria_id',
        'instituicao_id',
        'decimo_terceiro',
        'data_ano_mes',
        'guid'
    ];


    public function tipoPaganteFavorecido()
    {
        return $this->belongsTo(FinanceiroTipoPaganteFavorecido::class, 'tipo_pagante_favorecido_id');
    }

     /**
     * Get the plano conta that owns the lancamento.
     */
    public function planoConta()
    {
        return $this->belongsTo(FinanceiroPlanoConta::class, 'plano_conta_id');
    }

    /**
     * Get the caixa that owns the lancamento.
     */
    public function caixa()
    {
        return $this->belongsTo(FinanceiroCaixa::class, 'caixa_id');
    }

    /**
     * Get the lancamento pai of the lancamento.
     */
    public function lancamentoPai()
    {
        return $this->belongsTo(FinanceiroLancamento::class, 'lancamento_pai_id');
    }

    /**
     * Get the instituicao destino origem of the lancamento.
     */
    public function instituicaoDestinoOrigem()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_destino_origem_id');
    }

    /**
     * Get the hist distrito of the lancamento.
     */
    public function histDistrito()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'hist_distrito_id');
    }

    /**
     * Get the hist geral of the lancamento.
     */
    public function histGeral()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'hist_geral_id');
    }

    /**
     * Get the hist igreja of the lancamento.
     */
    public function histIgreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'hist_igreja_id');
    }

    /**
     * Get the hist orgao of the lancamento.
     */
    public function histOrgao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'hist_orgao_id');
    }

    /**
     * Get the hist regiao of the lancamento.
     */
    public function histRegiao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'hist_regiao_id');
    }

    /**
     * Get the hist secretaria of the lancamento.
     */
    public function histSecretaria()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'hist_secretaria_id');
    }

    /**
     * Get the instituicao that owns the lancamento.
     */
    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id');
    }

    public function anexos()
    {
        return $this->hasMany(Anexo::class, 'lancamento_id');
    }

    public function scopeWhereInstituicao($query, $instituicaoId = null)
    {
        return $query->when($instituicaoId,
            fn($q) => $q->where('instituicao_id', $instituicaoId),
            fn($q) => $q->where('instituicao_id', Identifiable::fetchSessionIgrejaLocal()->id)
        );
    }
}
