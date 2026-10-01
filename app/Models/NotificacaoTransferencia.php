<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class NotificacaoTransferencia extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'notificacoes_transferencias';

    protected $fillable = [
        'membro_id',
        'regiao_origem_id',
        'distrito_origem_id',
        'igreja_origem_id',
        'regiao_destino_id',
        'distrito_destino_id',
        'igreja_destino_id',
        'user_abertura',
        'dt_abertura',
        'user_finalizacao',
        'dt_aceite',
        'dt_rejeicao',
        'motivo_rejeicao',
    ];

    public function membro()
    {
        return $this->belongsTo(MembresiaMembro::class, 'membro_id', 'id');
    }

    public function regiaoOrigem()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_origem_id', 'id');
    }

    public function distritoOrigem()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'distrito_origem_id', 'id');
    }

    public function igrejaOrigem()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_origem_id', 'id');
    }

    public function regiaoDestino()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_destino_id', 'id');
    }

    public function distritoDestino()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'distrito_destino_id', 'id');
    }

    public function igrejaDestino()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_destino_id', 'id');
    }
}
