<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PessoaFuncaoministerial extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'pessoas_funcaoministerial';

    protected $fillable = [
        'funcao',
        'ordem',
        'titulo',
        'excluido',
        'created_at',
        'updated_at',
        'qtd_prebendas',
    ];

    // Relacionamento inverso com PessoaNomeacao
    public function nomeacoes()
    {
        return $this->hasMany(PessoaNomeacao::class, 'funcao_ministerial_id', 'id');
    }
}
