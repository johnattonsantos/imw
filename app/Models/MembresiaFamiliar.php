<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaFamiliar extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'membresia_familiares';

    protected $fillable = [
        'mae_nome', 
        'pai_nome', 
        'conjuge_nome',
        'data_casamento',
        'filhos',
        'historico_familiar',
        'membro_id'
    ];
}
