<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PessoaNomeacao extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'pessoas_nomeacoes';

    protected $fillable = [
        'codigo_host',
        'data_nomeacao',
        'data_termino',
        'hist_distrito_id',
        'hist_geral_id',
        'hist_igreja_id',
        'hist_orgao_id',
        'hist_regiao_id',
        'hist_secretaria_id',
        'instituicao_id',
        'pessoa_id',
        'funcao_ministerial_id'
    ];

    protected $casts = [
        'data_nomeacao' => 'date',
        'data_termino' => 'date'
    ];

    public function getDataNomeacaoAttribute($value)
    {
        if (!$value) return '';

        return Carbon::parse($value)->format('d/m/Y');
    }

    public function getDataTerminoAttribute($value)
    {
        if (!$value) return '';

        return Carbon::parse($value)->format('d/m/Y');
    }

    // Relacionamento com PessoaFuncaoministerial
    public function funcaoMinisterial()
    {
        return $this->belongsTo(PessoaFuncaoministerial::class, 'funcao_ministerial_id', 'id');
    }

    // Relacionamento com Instituicao
    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id', 'id');
    }

    public function pessoa()
    {
        return $this->belongsTo(PessoasPessoa::class, 'pessoa_id', 'id');
    }
}
