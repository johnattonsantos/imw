<?php

namespace App\Http\Requests;

use App\Calculators\PrebendasClerigos\MaxPrebendasClerigoCalculator;
use App\Rules\PrebendaExistis;
use App\Rules\TakeMaxPrebendaForAnoAndFuncaoMinisterial;
use Illuminate\Foundation\Http\FormRequest;

class StorePrebendaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $ano = $this->input('ano');
        $valor = $this->input('valor');

        return [

            'ano' => ['required', new PrebendaExistis($ano)],
            'valor' => ['required', new TakeMaxPrebendaForAnoAndFuncaoMinisterial(new MaxPrebendasClerigoCalculator(), $ano, $valor)],
        ];
    }


    public function messages()
    {
        return [
            'ano.required' => 'Você precisa informar o ano da prebenda.',
            'valor.required' => 'O valor da prebenda deve ser informado.',
        ];
    }
}
