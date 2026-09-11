<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ComunicacaoChatMensagemLeitura extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'comunicacao_chat_mensagem_leituras';

    protected $fillable = [
        'mensagem_id',
        'user_id',
        'pessoa_id',
        'lido_em',
    ];

    protected $casts = [
        'lido_em' => 'datetime',
    ];

    public function mensagem()
    {
        return $this->belongsTo(ComunicacaoChatMensagem::class, 'mensagem_id');
    }
}
