<?php

namespace App\Models\Patrimonio;

use App\Models\InstituicoesInstituicao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DocumentoPatrimonial extends Model
{
    use HasFactory;

    protected $table = 'patrimonio_documentos';

    protected $fillable = [
        'igreja_id',
        'nome',
        'tipo',
        'arquivo',
        'data_emissao',
        'data_validade',
        'status',
        'observacoes',
        'documentavel_type',
        'documentavel_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'igreja_id' => 'integer',
        'documentavel_id' => 'integer',
        'data_emissao' => 'date',
        'data_validade' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'alerta_vencimento',
    ];

    protected static function booted(): void
    {
        static::saving(function (DocumentoPatrimonial $documento) {
            $dataValidade = empty($documento->data_validade)
                ? null
                : Carbon::parse((string) $documento->data_validade);

            if ($dataValidade && $dataValidade->isPast()) {
                $documento->status = 'vencido';
                return;
            }

            if (empty($documento->status)) {
                $documento->status = 'vigente';
            }
        });
    }

    public function scopeDaIgreja(Builder $query, int $igrejaId): Builder
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeVencidos(Builder $query): Builder
    {
        return $query->whereDate('data_validade', '<', now()->toDateString());
    }

    public function scopeVencendoEmAte30Dias(Builder $query): Builder
    {
        return $query->whereNotNull('data_validade')
            ->whereDate('data_validade', '>=', now()->toDateString())
            ->whereDate('data_validade', '<=', now()->addDays(30)->toDateString());
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }

    public function documentavel()
    {
        return $this->morphTo();
    }

    public function riscosJuridicos()
    {
        return $this->hasMany(RiscoJuridico::class, 'documento_id');
    }

    public function benfeitorias()
    {
        return $this->hasMany(Benfeitoria::class, 'documento_id');
    }

    public function baixas()
    {
        return $this->hasMany(BaixaPatrimonial::class, 'documento_id');
    }

    public function getAlertaVencimentoAttribute(): bool
    {
        if (empty($this->data_validade)) {
            return false;
        }

        $hoje = now()->startOfDay();
        $limite = now()->addDays(30)->endOfDay();

        return $this->data_validade->between($hoje, $limite);
    }

    public function isVencido(): bool
    {
        return ! empty($this->data_validade) && $this->data_validade->isPast();
    }

    public function arquivoNomeOriginal(): string
    {
        return basename((string) $this->arquivo);
    }
}
