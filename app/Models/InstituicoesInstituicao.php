<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class InstituicoesInstituicao extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'instituicoes_instituicoes';

    protected $fillable = [
        'nome',
        'tipo_instituicao_id',
        'instituicao_pai_id',
        'codigo_host',
        'ativo',
        'bairro',
        'caw',
        'cep',
        'cidade',
        'cnpj',
        'complemento',
        'data_abertura',
        'ddd',
        'email',
        'endereco',
        'inss',
        'nome_fantasia',
        'numero',
        'pais',
        'site',
        'telefone',
        'uf',
        'pastor',
        'tesoureiro',
        'regiao_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'perfil_user')->withPivot('perfil_id');
    }

    public function perfils()
    {
        return $this->belongsToMany(Perfil::class, 'perfil_user')->withPivot('user_id');
    }

    public function instituicaoPai()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_pai_id');
    }

    public function tipoInstituicao()
    {
        return $this->belongsTo(InstituicoesTipoInstituicao::class, 'tipo_instituicao_id');
    }

    public function caixas()
    {
        return $this->hasMany(FinanceiroCaixa::class, 'instituicao_id')->orderBy('id', 'asc');
    }

    public function nomeacoes()
    {
        return $this->hasMany(PessoaNomeacao::class, 'instituicao_id', 'id');
    }
}
