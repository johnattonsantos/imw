<?php

namespace App\Exceptions;

use RuntimeException;

class RecadastramentoJaValidadoException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Este recadastramento já foi validado.');
    }
}
