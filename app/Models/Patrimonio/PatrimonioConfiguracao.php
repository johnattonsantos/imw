<?php

namespace App\Models\Patrimonio;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatrimonioConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'patrimonio_configuracoes';

    protected $fillable = [
        'tipo',
        'nome',
        'descricao',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'id' => 'integer',
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeDoTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }
}
