<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class GCeuMembros extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'gceu_membros';

    protected $guarded = [];

    public function membro()
    {
        return $this->belongsTo(MembresiaMembro::class, 'membro_id', 'id');
    }

    public function funcao()
    {
        return $this->belongsTo(GCeuFuncoes::class, 'gceu_funcao_id', 'id');
    }
}
