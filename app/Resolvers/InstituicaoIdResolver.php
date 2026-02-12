<?php

namespace App\Resolvers;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class InstituicaoIdResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        if ($auditable instanceof Model) {
            $modelInstituicaoId = $auditable->getAttribute('instituicao_id');
            if (! empty($modelInstituicaoId)) {
                return (int) $modelInstituicaoId;
            }
        }

        $sessionInstituicaoId = data_get(session('session_perfil'), 'instituicao_id');
        if (! empty($sessionInstituicaoId)) {
            return (int) $sessionInstituicaoId;
        }

        return null;
    }
}
