<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdLideranca extends Model
{
    use HasFactory;

    protected $table = 'ebd_liderancas';

    protected $fillable = [
        'membro_id',
        'cargo',
        'ativo',
        'data_inicio',
        'data_fim',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function membro()
    {
        return $this->belongsTo(MembresiaMembro::class, 'membro_id');
    }
}
