<?php

namespace App\Models\Patrimonio;

use App\Models\InstituicoesInstituicao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaixaPatrimonial extends Model
{
    use HasFactory;

    protected $table = 'patrimonio_baixas';

    protected $fillable = [
        'igreja_id',
        'imovel_id',
        'bem_movel_id',
        'motivo',
        'data_baixa',
        'responsavel',
        'documento_comprobatorio',
        'observacoes',
        'documento_id',
        'risco_juridico_id',
        'benfeitoria_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'igreja_id' => 'integer',
        'imovel_id' => 'integer',
        'bem_movel_id' => 'integer',
        'data_baixa' => 'date',
        'documento_id' => 'integer',
        'risco_juridico_id' => 'integer',
        'benfeitoria_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeDaIgreja(Builder $query, int $igrejaId): Builder
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeDoImovel(Builder $query, int $imovelId): Builder
    {
        return $query->where('imovel_id', $imovelId);
    }

    public function scopeDoBemMovel(Builder $query, int $bemMovelId): Builder
    {
        return $query->where('bem_movel_id', $bemMovelId);
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }

    public function imovel()
    {
        return $this->belongsTo(Imovel::class, 'imovel_id');
    }

    public function bemMovel()
    {
        return $this->belongsTo(BemMovel::class, 'bem_movel_id');
    }

    public function documento()
    {
        return $this->belongsTo(DocumentoPatrimonial::class, 'documento_id');
    }

    public function riscoJuridico()
    {
        return $this->belongsTo(RiscoJuridico::class, 'risco_juridico_id');
    }

    public function benfeitoria()
    {
        return $this->belongsTo(Benfeitoria::class, 'benfeitoria_id');
    }

    public function origemPrincipal(): ?string
    {
        if (! is_null($this->bem_movel_id)) {
            return 'bem_movel';
        }

        if (! is_null($this->imovel_id)) {
            return 'imovel';
        }

        return null;
    }

    public function motivoFormatado(): string
    {
        return trim((string) $this->motivo) !== '' ? (string) $this->motivo : '-';
    }
}
