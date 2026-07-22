<?php

namespace App\Rules;

use App\Calculators\PrebendasClerigos\MaxPrebendasClerigoCalculatorInterface;
use App\Traits\Identifiable;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Prebenda;

class TakeMaxPrebendaForAnoAndFuncaoMinisterial implements Rule
{
    protected $ano;
    protected $valor;
    protected $valorMaxPrebenda;

    private MaxPrebendasClerigoCalculatorInterface $calculator;


    public function __construct(MaxPrebendasClerigoCalculatorInterface $calculator, $ano, $valor)
    {
        $this->ano = $ano;
        $this->valor = $this->parseFloat($valor);
        $this->calculator = $calculator;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(!$this->ano) return false;

        $pessoa = Identifiable::fetchSessionPessoa();
        $prebenda = Prebenda::where('ano', $this->ano)->where('ativo', 1)->first();

        if (!$prebenda) {
            return false;
        }

        $this->valorMaxPrebenda = $this->calculator->calculate($pessoa, $prebenda);

        return $this->valor <= $this->valorMaxPrebenda ? true : false;
    }

    private function parseFloat($value)
    {
        if (is_string($value) && strpos($value, 'R$ ') === 0) {
            $value = substr($value, 3);
        }
        $normalizedValue = str_replace('.', '', $value);
        $normalizedValue = str_replace(',', '.', $normalizedValue);
        return (float) $normalizedValue;
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O valor da prebenda não pode ultrapassar o valor máximo de R$ ' . number_format((float) $this->valorMaxPrebenda, 2, ',', '.');
    }
}
