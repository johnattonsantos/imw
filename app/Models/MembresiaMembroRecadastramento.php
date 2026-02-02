<?php

namespace App\Models;

use App\Traits\Identifiable;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaMembroRecadastramento extends Model implements Auditable
{
    use HasFactory, SoftDeletes, HasUuids, Compoships, AuditableTrait;

    // status
    const STATUS_ATIVO = 'A';
    const STATUS_INATIVO = 'I';

    // vinculo
    const VINCULO_VISITANTE = 'V';
    const VINCULO_CONGREGADO = 'C';
    const VINCULO_MEMBRO = 'M';

    protected $table = 'membresia_migracao';

    protected $fillable = [
        'status',
        'nome',
        'sexo',
        'data_nascimento',
        'estado_civil',
        'nacionalidade',
        'naturalidade',
        'uf',
        'historico',
        'cpf',
        'distrito_id',
        'igreja_id',
        'regiao_id',
        'gceu_id',
        'escolaridade_id',
        'novo_convertido',
        'profissao',
        'documento',
        'documento_complemento',
        'tipo_documento',
        'foto',
        'funcao_eclesiastica_id',
        'rol_atual',
        'igreja_host',
        'data_conversao',
        'data_batismo',
        'data_batismo_espirito',
        'vinculo',
        'congregacao_host',
        'codigo_host',
        'congregacao_id',
        'has_errors',
    ];

    protected $casts = [
        'data_nascimento' => 'date'
    ];

    public function contato()
    {
        return $this->hasOne(MembresiaContato::class, 'membro_id');
    }

    public function familiar()
    {
        return $this->hasOne(MembresiaFamiliar::class, 'membro_id');
    }

    public function funcoesMinisteriais()
    {
        return $this->hasMany(MembresiaFuncaoMinisterial::class, 'membro_id');
    }

    public function formacoesEclesiasticas() {
        return $this->hasMany(MembresiaFormacaoEclesiastica::class, 'membro_id');
    }

    public function congregacao()
    {
        return $this->belongsTo(CongregacoesCongregacao::class, 'congregacao_id');
    }

    public function gceu()
    {
        return $this->belongsTo(GCeu::class, 'gceu_id');
    }

    public function rolPermanente()
    {
        return $this->hasMany(MembresiaRolPermanente::class, 'membro_id');
    }

    public function rolAtualSessionIgreja(): HasOne
    {
        return $this->hasOne(MembresiaRolPermanente::class, 'membro_id', 'id')->withTrashed()->where('lastrec', 1)->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id);
    }

    public function disciplinas()
    {
        return $this->hasMany(MembresiaDisciplina::class, 'membro_id');
    }
    
    public function notificacaoTransferenciaAtiva()
    {
        return $this->hasOne(NotificacaoTransferencia::class, 'membro_id')->whereNull('dt_aceite')->whereNull('dt_rejeicao');
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'A':
                return 'ATIVO';

            case 'I':
                return 'INATIVO';
        }
    }

    public function getVinculoTextAttribute()
    {
        switch ($this->vinculo) {
            case 'M':
                return 'MEMBRO';
            case 'C':
                return 'CONGREGADO';
            case 'V':
                return 'VISITANTE';
        }
    }
}
