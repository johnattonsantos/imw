<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class PeriodoEclesiasticoCorrenteRule implements Rule
{
    private ?Carbon $inicio = null;
    private ?Carbon $limite = null;

    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value)) {
            return false;
        }

        $hoje = Carbon::today();
        $anoInicio = $hoje->month >= 11 ? $hoje->year : $hoje->year - 1;
        $this->inicio = Carbon::create($anoInicio, 11, 1)->startOfDay();
        $fimPeriodo = Carbon::create($anoInicio + 1, 10, 31)->endOfDay();
        $this->limite = $fimPeriodo->lessThan($hoje) ? $fimPeriodo : $hoje;

        $data = Carbon::parse($value)->startOfDay();

        return $data->betweenIncluded($this->inicio, $this->limite);
    }

    public function message()
    {
        $inicio = optional($this->inicio)->format('d/m/Y') ?: '01/11';
        $limite = optional($this->limite)->format('d/m/Y') ?: __('a data atual');

        return __('A data deve estar dentro do período eclesiástico corrente (:inicio a :fim).', [
            'inicio' => $inicio,
            'fim' => $limite,
        ]);
    }
}
