<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaRolPermanente extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Compoships, AuditableTrait;

    const STATUS_RECEBIMENTO = 'A';
    const STATUS_EXCLUSAO = 'I';
    const STATUS_TRANSFERENCIA = 'T';

    protected $table = 'membresia_rolpermanente';

    protected $fillable = [
        'lastrec',
        'status',
        'numero_rol',
        'codigo_host',
        'dt_recepcao',
        'dt_exclusao',
        'clerigo_id',
        'distrito_id',
        'igreja_id',
        'membro_id',
        'modo_exclusao_id',
        'modo_recepcao_id',
        'regiao_id',
        'congregacao_id'
    ];

    protected $casts = [
        'dt_recepcao' => 'date',
        'dt_exclusao' => 'date',
    ];
    
    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }

    public function modoExclusao()
    {
        return $this->belongsTo(MembresiaSituacao::class, 'modo_exclusao_id');
    }

    public function modoRecepcao()
    {
        return $this->belongsTo(MembresiaSituacao::class, 'modo_recepcao_id');
    }

    public function clerigo() {
        return $this->belongsTo(PessoasPessoa::class, 'clerigo_id');
    }

    public function congregacao() {
        return $this->belongsTo(CongregacoesCongregacao::class, 'congregacao_id');
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'A':
                return 'ATIVO';
            
            case 'I':
                return 'INATIVO';

            case 'T':
                return 'TRANSFERÊNCIA';            
            }
    }
}
