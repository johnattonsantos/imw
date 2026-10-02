<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidaCPF implements Rule
{
    private $message = 'CPF inválido. Verifique os números informados.';
 
    public function __construct()
    {
        //
    }


    public function passes($attribute, $value)
    {
        if ($value === null || trim((string) $value) === '') {
            return true;
        }
        
        $cpf = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cpf) != 11) {
            $this->message = 'O CPF deve conter exatamente 11 dígitos.';
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $this->message = 'CPF inválido. Verifique os números informados.';
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $this->message = 'CPF inválido. Verifique os números informados.';
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
