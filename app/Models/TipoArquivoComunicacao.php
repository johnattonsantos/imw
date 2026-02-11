<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class TipoArquivoComunicacao extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'tipo_arquivo_comunicacao';

    protected $fillable = [
        'instituicao_id',
        'extensao',
    ];

    public function instituicao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'instituicao_id');
    }
}
