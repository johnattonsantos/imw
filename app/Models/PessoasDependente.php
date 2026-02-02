<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PessoasDependente extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = "pessoas_dependentes";

    protected $fillable = [
        'pessoa_id',
        'nome',
        'cpf',
        'data_nascimento',
        'parentesco',
        'sexo',
        'declarar_em_irpf',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'declarar_em_irpf' => 'boolean',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(PessoasPessoa::class, 'pessoa_id');
    }
}
