<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PessoasPessoa extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'pessoas_pessoas';
    protected $guarded = [];
    // protected $fillable = [
    //     'codigo_host',
    //     'tipo',
    //     'nome',
    //     'status',
    //     'foto',
    //     'identidade',
    //     'orgao_emissor',
    //     'data_emissao',
    //     'cpf',
    //     'inss',
    //     'endereco',
    //     'numero',
    //     'complemento',
    //     'bairro',
    //     'cidade',
    //     'uf',
    //     'pais',
    //     'cep',
    //     'email',
    //     'estado_civil',
    //     'data_nascimento',
    //     'situacao_id',
    //     'pessoas_pessoacol',
    //     'distrito_id',
    //     'igreja_id',
    //     'regiao_id',
    //     'sexo',
    //     'raca',
    //     'escolaridade',
    //     'natural_cidade',
    //     'natural_uf',
    //     'nome_conjuge',
    //     'nome_mae',
    //     'nome_pai',
    //     'telefone_alternativo',
    //     'telefone_preferencial',
    //     'certidao_civil',
    //     'ctps',
    //     'ctps_emissao',
    //     'habilitacao',
    //     'habilitacao_categoria',
    //     'habilitacao_emissor',
    //     'habilitacao_uf',
    //     'identidade_uf',
    //     'pispasep',
    //     'pispasep_emissao',
    //     'reservista',
    //     'reservista_secao',
    //     'reservista_serie',
    //     'residencia_propria',
    //     'residencia_propria_fgts',
    //     'titulo_eleitor',
    //     'titulo_eleitor_secao',
    //     'titulo_eleitor_zona',
    //     'ficha_completa_ok',
    //     'ficha_skip',
    //     'isento_pis',
    //     'isento_reservista',
    //     'isento_titulo_eleitor',
    //     'formacao_id',
    //     'categoria'
    // ];

    public function nomeacoes()
    {
        return $this->hasMany(PessoaNomeacao::class, 'pessoa_id', 'id');
    }

    public function nomeacoesAtivas()
    {
        return $this->hasMany(PessoaNomeacao::class, 'pessoa_id', 'id')->whereNull('data_termino');
    }

    public function dependentes(): HasMany
    {
        return $this->hasMany(PessoasDependente::class, 'pessoa_id', 'id');
    }

    public function prebendas()
    {
        return $this->hasMany(PessoasPrebenda::class, 'pessoa_id');
    }

    public function regiao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_id', 'id');
    }
}
