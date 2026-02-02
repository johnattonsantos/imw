<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaFormacaoEclesiastica extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'membresia_formacoeseclesiasticas';

    protected $fillable = ['inicio', 'termino', 'observacao', 'curso_id', 'membro_id'];

    public function membro()
    {
        return $this->belongsTo(MembresiaMembro::class, 'membro_id', 'id');
    }

    public function curso()
    {
        return $this->belongsTo(MembresiaCurso::class, 'curso_id', 'id');
    }
}
