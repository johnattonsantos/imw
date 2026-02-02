<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaSituacao extends Model implements Auditable
{
    const TIPO_ADESAO = 'R';
    const TIPO_EXCLUSAO = 'E';
    const TIPO_DISCIPLINA = 'D';

    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'membresia_situacoes';

    protected $fillable = ['descricao', 'tipo', 'nome'];
}
