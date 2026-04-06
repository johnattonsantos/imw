<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComunicacaoLeituraIgreja extends Model
{
    use HasFactory;

    protected $table = 'comunicacao_leituras_igrejas';

    protected $fillable = [
        'comunicacao_id',
        'igreja_id',
        'user_id',
        'lido_em',
    ];

    protected $casts = [
        'lido_em' => 'datetime',
    ];
}

