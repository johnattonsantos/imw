<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Perfil extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    const NIVEL_IGREJA = 'I';
    const NIVEL_DISTRITO = 'D';
    const NIVEL_REGIAO = 'R';


    protected $fillable = ['nome', 'nivel'];

    public function regras()
    {
        return $this->belongsToMany(Regra::class, 'perfil_regra');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'perfil_user');
    }
}
