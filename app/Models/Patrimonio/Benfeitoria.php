<?php

namespace App\Models\Patrimonio;

use App\Models\InstituicoesInstituicao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benfeitoria extends Model
{
    use HasFactory;

    protected $table = 'patrimonio_benfeitorias';

    protected $fillable = [
        'igreja_id',
        'imovel_id',
        'descricao',
        'data',
        'valor_investido',
        'responsavel',
        'documento_anexo',
        'observacoes',
        'bem_movel_id',
        'documento_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'igreja_id' => 'integer',
        'imovel_id' => 'integer',
        'data' => 'date',
        'valor_investido' => 'decimal:2',
        'bem_movel_id' => 'integer',
        'documento_id' => 'integer',
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

    public function baixas()
    {
        return $this->hasMany(BaixaPatrimonial::class, 'benfeitoria_id');
    }

    public function valorInvestidoFormatado(): string
    {
        return 'R$ ' . number_format((float) $this->valor_investido, 2, ',', '.');
    }

    public function possuiBaixas(): bool
    {
        return $this->relationLoaded('baixas')
            ? $this->baixas->isNotEmpty()
            : $this->baixas()->exists();
    }
}
