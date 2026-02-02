<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaFuncaoMinisterial extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'membresia_funcoesministeriais';

    protected $fillable = [
        'data_entrada',
        'data_saida',
        'observacoes',
        'membro_id',
        'setor_id',
        'tipoatuacao_id',
    ];

    protected $casts = [
        'data_entrada' => 'date',
        'data_saida'   => 'date'
    ];

    public function ministerio()
    {
        return $this->belongsTo(MembresiaSetor::class, 'setor_id');
    }

    public function tipoAtuacao()
    {
        return $this->belongsTo(MembresiaTipoAtuacao::class, 'tipoatuacao_id');
    }
}
