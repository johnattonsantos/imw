<?php

namespace App\Models\Patrimonio;

use App\Models\InstituicoesInstituicao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BemMovel extends Model
{
    use HasFactory;

    protected $table = 'patrimonio_bens_moveis';

    protected $fillable = [
        'igreja_id',
        'imovel_id',
        'placa_patrimonial',
        'nome',
        'categoria',
        'descricao',
        'estado_conservacao',
        'localizacao',
        'responsavel',
        'data_aquisicao',
        'valor_aquisicao',
        'valor_residual',
        'vida_util',
        'natureza_comprobatoria',
        'numero_documento',
        'fornecedor_doador',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'id' => 'integer',
        'igreja_id' => 'integer',
        'imovel_id' => 'integer',
        'vida_util' => 'integer',
        'data_aquisicao' => 'date',
        'valor_aquisicao' => 'decimal:2',
        'valor_residual' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (BemMovel $bemMovel) {
            if (empty($bemMovel->codigo_patrimonial)) {
                $bemMovel->codigo_patrimonial = self::gerarCodigoPatrimonial();
            }

            $bemMovel->qr_code_patrimonial = self::gerarQrCodePatrimonial($bemMovel->codigo_patrimonial);
        });

        static::updating(function (BemMovel $bemMovel) {
            if (empty($bemMovel->codigo_patrimonial)) {
                $bemMovel->codigo_patrimonial = self::gerarCodigoPatrimonial();
            }

            if ($bemMovel->isDirty('codigo_patrimonial') || empty($bemMovel->qr_code_patrimonial)) {
                $bemMovel->qr_code_patrimonial = self::gerarQrCodePatrimonial($bemMovel->codigo_patrimonial);
            }
        });
    }

    public function scopeDaIgreja(Builder $query, int $igrejaId): Builder
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeDoImovel(Builder $query, int $imovelId): Builder
    {
        return $query->where('imovel_id', $imovelId);
    }

    public function scopeComRelacionamentos(Builder $query): Builder
    {
        return $query->with(['documentos', 'baixas', 'ultimaBaixa', 'imovel']);
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }

    public function imovel()
    {
        return $this->belongsTo(Imovel::class, 'imovel_id');
    }

    public function documentos()
    {
        return $this->morphMany(DocumentoPatrimonial::class, 'documentavel');
    }

    public function riscosJuridicos()
    {
        return $this->hasMany(RiscoJuridico::class, 'bem_movel_id');
    }

    public function benfeitorias()
    {
        return $this->hasMany(Benfeitoria::class, 'bem_movel_id');
    }

    public function baixas()
    {
        return $this->hasMany(BaixaPatrimonial::class, 'bem_movel_id');
    }

    public function ultimaBaixa()
    {
        return $this->hasOne(BaixaPatrimonial::class, 'bem_movel_id')->latestOfMany();
    }

    public function totalDocumentos(): int
    {
        return $this->relationLoaded('documentos')
            ? $this->documentos->count()
            : $this->documentos()->count();
    }

    public function totalBaixas(): int
    {
        return $this->relationLoaded('baixas')
            ? $this->baixas->count()
            : $this->baixas()->count();
    }

    public function possuiBaixas(): bool
    {
        return $this->totalBaixas() > 0;
    }

    public static function gerarCodigoPatrimonial(): string
    {
        do {
            $codigo = 'BM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::query()->where('codigo_patrimonial', $codigo)->exists());

        return $codigo;
    }

    public static function gerarQrCodePatrimonial(string $codigoPatrimonial): string
    {
        return 'PATRIMONIO:BEM_MOVEL:' . trim($codigoPatrimonial);
    }

    public function getQrCodeUrlAttribute(): string
    {
        $payload = urlencode($this->qr_code_patrimonial ?: self::gerarQrCodePatrimonial((string) $this->codigo_patrimonial));

        return "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={$payload}";
    }
}
