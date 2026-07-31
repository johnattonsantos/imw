<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class EventoInscricao extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'evento_inscricoes';

    protected $fillable = [
        'evento_id',
        'origem',
        'membro_id',
        'pessoa_id',
        'cpf',
        'qr_token',
        'nome',
        'funcao_eclesiastica',
        'igreja_id',
        'igreja_nome',
        'telefone',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function membro()
    {
        return $this->belongsTo(MembresiaMembro::class, 'membro_id');
    }

    public function clerigo()
    {
        return $this->belongsTo(PessoasPessoa::class, 'pessoa_id');
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }

    public function movimentos()
    {
        return $this->hasMany(EventoInscricaoMovimento::class, 'evento_inscricao_id')->orderByDesc('registrado_em');
    }

    public function ultimoMovimento()
    {
        return $this->hasOne(EventoInscricaoMovimento::class, 'evento_inscricao_id')->latestOfMany('registrado_em');
    }
}
