<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ComunicacaoChatMensagem extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'comunicacao_chat_mensagens';

    protected $fillable = [
        'conversa_id',
        'remetente_user_id',
        'remetente_pessoa_id',
        'conteudo',
        'status_entrega',
        'enviado_em',
    ];

    protected $casts = [
        'enviado_em' => 'datetime',
    ];

    public function conversa()
    {
        return $this->belongsTo(ComunicacaoChatConversa::class, 'conversa_id');
    }

    public function remetentePessoa()
    {
        return $this->belongsTo(PessoasPessoa::class, 'remetente_pessoa_id');
    }

    public function leituras()
    {
        return $this->hasMany(ComunicacaoChatMensagemLeitura::class, 'mensagem_id');
    }
}
