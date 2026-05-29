<?php

namespace App\Models\Patrimonio;

use App\Models\InstituicoesInstituicao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Imovel extends Model
{
    use HasFactory;

    protected $table = 'patrimonio_imoveis';

    protected $fillable = [
        'igreja_id',
        'codigo_patrimonial',
        'natureza_imovel',
        'nome',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'latitude',
        'longitude',
        'area_total',
        'area_construida',
        'iptu_itr',
        'inscricao_municipal_rural',
        'valor_historico',
        'valor_venal',
        'valor_mercado',
        'situacao_tributaria',
        'cnpj_utilizado',
        'status_titularidade',
        'numero_matricula',
        'cartorio',
        'tipo_titulo',
        'data_aquisicao_posse',
        'possui_escritura_registrada',
        'regularizacao_pendente',
        'observacoes_juridicas',
        'avcb_validade',
    ];

    protected $casts = [
        'id' => 'integer',
        'igreja_id' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'area_total' => 'decimal:2',
        'area_construida' => 'decimal:2',
        'valor_historico' => 'decimal:2',
        'valor_venal' => 'decimal:2',
        'valor_mercado' => 'decimal:2',
        'data_aquisicao_posse' => 'date',
        'possui_escritura_registrada' => 'boolean',
        'regularizacao_pendente' => 'boolean',
        'avcb_validade' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Imovel $imovel) {
            if (empty($imovel->codigo_patrimonial)) {
                $imovel->codigo_patrimonial = self::gerarCodigoPatrimonial();
            }

            $imovel->regularizacao_pendente = $imovel->calcularRegularizacaoPendente();
        });

        static::updating(function (Imovel $imovel) {
            if (empty($imovel->codigo_patrimonial)) {
                $imovel->codigo_patrimonial = self::gerarCodigoPatrimonial();
            }

            $imovel->regularizacao_pendente = $imovel->calcularRegularizacaoPendente();
        });
    }

    public function scopeDaIgreja(Builder $query, int $igrejaId): Builder
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeComRelacionamentos(Builder $query): Builder
    {
        return $query->with(['documentos', 'riscosJuridicos', 'benfeitorias', 'bensMoveis']);
    }

    public function scopeComRegularizacaoPendente(Builder $query): Builder
    {
        return $query->where('regularizacao_pendente', true);
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }

    public function bensMoveis()
    {
        return $this->hasMany(BemMovel::class, 'imovel_id');
    }

    public function documentos()
    {
        return $this->morphMany(DocumentoPatrimonial::class, 'documentavel');
    }

    public function riscosJuridicos()
    {
        return $this->hasMany(RiscoJuridico::class, 'imovel_id');
    }

    public function benfeitorias()
    {
        return $this->hasMany(Benfeitoria::class, 'imovel_id');
    }

    public function baixas()
    {
        return $this->hasMany(BaixaPatrimonial::class, 'imovel_id');
    }

    public function totalDocumentos(): int
    {
        return $this->relationLoaded('documentos')
            ? $this->documentos->count()
            : $this->documentos()->count();
    }

    public function totalRiscosJuridicos(): int
    {
        return $this->relationLoaded('riscosJuridicos')
            ? $this->riscosJuridicos->count()
            : $this->riscosJuridicos()->count();
    }

    public function totalBenfeitorias(): int
    {
        return $this->relationLoaded('benfeitorias')
            ? $this->benfeitorias->count()
            : $this->benfeitorias()->count();
    }

    public function totalValorBenfeitorias(): float
    {
        if ($this->relationLoaded('benfeitorias')) {
            return (float) $this->benfeitorias->sum('valor_investido');
        }

        return (float) $this->benfeitorias()->sum('valor_investido');
    }

    public function calcularRegularizacaoPendente(): bool
    {
        if (blank($this->numero_matricula)) {
            return true;
        }

        if (! (bool) $this->possui_escritura_registrada) {
            return true;
        }

        $status = Str::of((string) $this->status_titularidade)
            ->lower()
            ->ascii()
            ->squish()
            ->value();

        return in_array($status, [
            'posse contratual',
            'cessao de direitos',
            'contrato particular',
        ], true);
    }

    public function tituloExibicao(): string
    {
        return trim((string) $this->nome) !== '' ? (string) $this->nome : ('Imóvel #' . $this->id);
    }

    public static function gerarCodigoPatrimonial(): string
    {
        do {
            $codigo = 'IM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::query()->where('codigo_patrimonial', $codigo)->exists());

        return $codigo;
    }
}
