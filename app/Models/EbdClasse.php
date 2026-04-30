<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbdClasse extends Model
{
    use HasFactory;

    protected $table = 'ebd_classes';

    protected $fillable = [
        'nome',
        'faixa_etaria',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function turmas()
    {
        return $this->hasMany(EbdTurma::class, 'classe_id');
    }
}
