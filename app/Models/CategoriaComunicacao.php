<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CategoriaComunicacao extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'categoria_comunicacao';

    protected $fillable = [
        'instituicao_id',
        'nome',
    ];

    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id');
    }

    public function comunicacoes()
    {
        return $this->hasMany(Comunicacao::class, 'categoria_comunicacao_id');
    }
}
