<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class InstituicoesTipoInstituicao extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    const IGREJA_LOCAL = 1;
    const DISTRITO = 2;
    const REGIAO = 3;
    const SECRETARIA_REGIONAL = 5;
    const IGREJA_GERAL = 6;
    const ORGAO_GERAL = 8;
    const SECRETARIA = 9;
    const CONTABILIDADE = 11;
    const CONGREGACAO = 13;
    const ESTATISTICA = 14;

    protected $table = 'instituicoes_tiposinstituicao';

    protected $fillable = ['nome', 'cor', 'sigla', 'hierarquia'];

    public function instituicoes()
    {
        return $this->hasMany(InstituicoesInstituicao::class, 'tipo_instituicao_id');
    }
}
