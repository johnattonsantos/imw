<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ComunicacaoChatConversa extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    public const TIPO_PRIVADA = 'PRIVADA';
    public const TIPO_DISTRITO = 'DISTRITO';
    public const TIPO_REGIAO = 'REGIAO';

    public const STATUS_ATIVA = 'ativa';
    public const STATUS_ARQUIVADA = 'arquivada';
    public const STATUS_ENCERRADA = 'encerrada';

    protected $table = 'comunicacao_chat_conversas';

    protected $fillable = [
        'tipo',
        'status',
        'remetente_user_id',
        'remetente_pessoa_id',
        'destinatario_pessoa_id',
        'igreja_id',
        'distrito_id',
        'regiao_id',
        'titulo',
    ];

    public function mensagens()
    {
        return $this->hasMany(ComunicacaoChatMensagem::class, 'conversa_id');
    }

    public function ultimaMensagem()
    {
        return $this->hasOne(ComunicacaoChatMensagem::class, 'conversa_id')->latestOfMany('enviado_em');
    }

    public function participantes()
    {
        return $this->hasMany(ComunicacaoChatParticipante::class, 'conversa_id');
    }

    public function remetentePessoa()
    {
        return $this->belongsTo(PessoasPessoa::class, 'remetente_pessoa_id');
    }

    public function destinatarioPessoa()
    {
        return $this->belongsTo(PessoasPessoa::class, 'destinatario_pessoa_id');
    }

    public function regiao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_id');
    }

    public function distrito()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'distrito_id');
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }
}
