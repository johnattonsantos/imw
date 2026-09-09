<?php

namespace App\Models\Juridico;

use App\Models\InstituicoesInstituicao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class RegiaoAcao extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'juridico_regiao_acoes';

    protected $fillable = [
        'regiao_id',
        'instituicao_id',
        'advogado_causa_id',
        'advogado_oposicao_id',
        'numero_processo',
        'autor',
        'reu',
        'vara_tribunal',
        'advogado_oposicao_nome',
        'status',
        'resultado',
        'data_distribuicao',
        'data_sentenca',
        'custo_demanda',
        'objeto',
        'teor_decisao',
        'outros',
        'observacoes',
    ];

    protected $casts = [
        'data_distribuicao' => 'date',
        'data_sentenca' => 'date',
        'custo_demanda' => 'decimal:2',
    ];

    public function regiao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_id');
    }

    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id');
    }

    public function advogadoCausa()
    {
        return $this->belongsTo(RegiaoAdvogado::class, 'advogado_causa_id');
    }

    public function advogadoOposicao()
    {
        return $this->belongsTo(RegiaoAdvogado::class, 'advogado_oposicao_id');
    }
}
