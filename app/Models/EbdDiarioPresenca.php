<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdDiarioPresenca extends Model
{
    use HasFactory;

    protected $table = 'ebd_diario_presencas';

    protected $fillable = [
        'diario_id',
        'aluno_id',
        'presente',
        'justificativa',
    ];

    protected $casts = [
        'presente' => 'boolean',
    ];

    public function diario()
    {
        return $this->belongsTo(EbdDiario::class, 'diario_id');
    }

    public function aluno()
    {
        return $this->belongsTo(EbdAluno::class, 'aluno_id');
    }
}
