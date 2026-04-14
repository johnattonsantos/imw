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
    const NIVEL_SISTEMA = 'S';

    const CODIGO_ADMINISTRADOR_SISTEMA = 'administrador_sistema';
    const CODIGO_CRIE = 'crie';


    protected $fillable = ['nome', 'nivel'];

    public static function correspondeCodigo(?string $nomePerfil, string $codigo): bool
    {
        $normalizado = self::normalizarNome($nomePerfil);
        $aliasesNormalizados = array_map(
            fn (string $alias): string => self::normalizarNome($alias),
            self::aliasesPorCodigo($codigo)
        );

        return in_array($normalizado, $aliasesNormalizados, true);
    }

    public static function aliasesPorCodigo(string $codigo): array
    {
        if ($codigo === self::CODIGO_ADMINISTRADOR_SISTEMA) {
            return [
                'administrador_sistema',
                'administrador do sistema',
            ];
        }

        if ($codigo === self::CODIGO_CRIE) {
            return [
                'crie',
            ];
        }

        return [self::normalizarNome($codigo)];
    }

    public static function normalizarNome(?string $nome): string
    {
        $valor = mb_strtolower(trim((string) $nome), 'UTF-8');
        $valor = str_replace(['-', '_'], ' ', $valor);
        $valor = preg_replace('/\s+/', ' ', $valor) ?? $valor;

        return $valor;
    }

    public function regras()
    {
        return $this->belongsToMany(Regra::class, 'perfil_regra');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'perfil_user');
    }
}
