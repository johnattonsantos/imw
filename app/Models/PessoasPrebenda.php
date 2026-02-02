<?php

namespace App\Models;

use App\Traits\FormatterUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PessoasPrebenda extends Model implements Auditable
{
    use HasFactory, FormatterUtils, AuditableTrait;

    protected $table = 'pessoas_prebendas';

    protected $fillable = [
        'pessoa_id',
        'ano',
        'valor'

    ];

    public function pessoas()
    {
        return $this->belongsTo(PessoasPessoa::class, 'pessoa_id');
    }

    public function nomeacoes()
    {
        return $this->hasManyThrough(
            PessoaNomeacao::class,
            PessoasPessoa::class,
            'id',
            'pessoa_id',
            'pessoa_id',
            'id'
        );
    }

    public function getValorFormatadoAttribute()
    {
        $valor = $this->valor ?? 0;
        return $this->formatCurrencyBRL($valor, true);

    }
}
