<?php

namespace App\Policies;

use App\Exceptions\MembroNotFoundException;
use App\Exceptions\UnauthorizedRouteException;
use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Models\User;
use App\Traits\Identifiable;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembresiaMembroPolicy
{
    use HandlesAuthorization, Identifiable;
    public function checkSameChurch(User $user, $membroId)
    {
        $pessoa = MembresiaMembro::findOr($membroId, fn() => throw new MembroNotFoundException());

        $igrejaSessionId = Identifiable::fetchSessionIgrejaLocal()->id;

        if ((int) $pessoa->igreja_id === (int) $igrejaSessionId) {
            return true;
        }

        // Fallback para registros antigos/incompletos: valida pela última linha do rol.
        $igrejaRolAtual = MembresiaRolPermanente::where('membro_id', $pessoa->id)
            ->where('lastrec', 1)
            ->orderByDesc('id')
            ->value('igreja_id');

        if ((int) $igrejaRolAtual === (int) $igrejaSessionId) {
            return true;
        }

        throw new UnauthorizedRouteException();
    }
}
