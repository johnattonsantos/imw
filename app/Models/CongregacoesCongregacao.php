<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class CongregacoesCongregacao extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;
    
    protected $table = 'congregacoes_congregacoes';

    protected $fillable = [
        'nome',
        'instituicao_id',
        'ativo',
        'bairro',
        'cep',
        'cidade',
        'codigo_host',
        'codigo_host_igreja',
        'complemento',
        'data_abertura',
        'ddd',
        'email',
        'endereco',
        'host_dirigente',
        'numero',
        'pais',
        'site',
        'telefone',
        'uf',
        'data_extincao',
    ];

    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id', 'id');
    }
}
