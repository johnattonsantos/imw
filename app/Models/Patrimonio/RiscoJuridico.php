<?php

namespace App\Models\Patrimonio;

use App\Models\InstituicoesInstituicao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RiscoJuridico extends Model
{
    use HasFactory;

    protected $table = 'patrimonio_riscos_juridicos';

    protected $fillable = [
        'igreja_id',
        'imovel_id',
        'possui_onus',
        'tipo_onus',
        'descricao',
        'nivel_risco',
        'data_identificacao',
        'providencia_recomendada',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'igreja_id' => 'integer',
        'imovel_id' => 'integer',
        'possui_onus' => 'boolean',
        'data_identificacao' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeDaIgreja(Builder $query, int $igrejaId): Builder
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeSemBaixas(Builder $query): Builder
    {
        return $query->whereDoesntHave('baixas');
    }

    public function scopeAltosECriticos(Builder $query): Builder
    {
        return $query->whereIn('nivel_risco', ['alto', 'critico']);
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }

    public function imovel()
    {
        return $this->belongsTo(Imovel::class, 'imovel_id');
    }

    public function baixas()
    {
        return $this->hasMany(BaixaPatrimonial::class, 'risco_juridico_id');
    }

    public function possuiBaixas(): bool
    {
        return $this->relationLoaded('baixas')
            ? $this->baixas->isNotEmpty()
            : $this->baixas()->exists();
    }

    public function riscoLabel(): string
    {
        return match ($this->nivel_risco) {
            'critico' => 'Crítico',
            'alto' => 'Alto',
            'medio' => 'Médio',
            default => 'Baixo',
        };
    }
}
