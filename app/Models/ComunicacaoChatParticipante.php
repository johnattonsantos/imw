<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ComunicacaoChatParticipante extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'comunicacao_chat_participantes';

    protected $fillable = [
        'conversa_id',
        'user_id',
        'pessoa_id',
        'igreja_id',
        'distrito_id',
        'regiao_id',
        'lido_em',
    ];

    protected $casts = [
        'lido_em' => 'datetime',
    ];

    public function conversa()
    {
        return $this->belongsTo(ComunicacaoChatConversa::class, 'conversa_id');
    }

    public function pessoa()
    {
        return $this->belongsTo(PessoasPessoa::class, 'pessoa_id');
    }
}
