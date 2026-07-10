<?php

namespace App\Models\Juridico;

use App\Models\InstituicoesInstituicao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class RegiaoAdvogado extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'juridico_regiao_advogados';

    protected $fillable = [
        'regiao_id',
        'nome',
        'tipo',
        'registro_oab',
        'telefone',
        'email',
        'contatos',
        'endereco_escritorio',
        'observacoes',
    ];

    public function regiao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_id');
    }
}
