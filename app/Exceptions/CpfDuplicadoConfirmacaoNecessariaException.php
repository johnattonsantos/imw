<?php

namespace App\Exceptions;

use App\Models\MembresiaMembro;
use RuntimeException;

class CpfDuplicadoConfirmacaoNecessariaException extends RuntimeException
{
    public function __construct(
        private readonly MembresiaMembro $membro,
        string $message
    ) {
        parent::__construct($message);
    }

    public function membro(): MembresiaMembro
    {
        return $this->membro;
    }
}
