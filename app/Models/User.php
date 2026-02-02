<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, AuditableTrait;

    const GERAL = 'G';
    const LOCAL = 'L';
    const ADMSISTEMA = 'S';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'telefone',
        'pessoa_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $auditExclude = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function perfilUser()
    {
        return $this->hasMany(PerfilUser::class, 'user_id');
    }

    public function perfilUserInstituicao()
    {
        return $this->hasMany(PerfilUser::class, 'user_id')->where('instituicao_id', session()->get('session_perfil')->instituicao_id);
    }

    public function instituicoes()
    {
        return $this->belongsToMany(InstituicoesInstituicao::class, 'user_instituicoes', 'user_id', 'instituicao_id', 'users.id', 'instituicoes_instituicoes.id');
    }

    public function pessoa(): HasOne
    {
        return $this->hasOne(PessoasPessoa::class, 'id', 'pessoa_id');
    }

    public function perfis()
    {
        return $this->belongsToMany(Perfil::class, 'perfil_user');
    }

    /* public function hasPerfilRegra($regraNome)
    {
        return $this->perfis->flatMap->regras->contains('nome', $regraNome);
    } */


    public function hasPerfilRegra($regraNome)
    {
        $sessionId = session()->get('session_perfil')->instituicao_id;
        $perfilId = session()->get('session_perfil')->perfil_id;

        return $this->perfis()
                    ->where('instituicao_id', $sessionId)
                    ->where('perfil_id', $perfilId)
                    ->whereHas('regras', function ($query) use ($regraNome) {
                        $query->where('nome', $regraNome);
                    })
                    ->exists();
    }

}
