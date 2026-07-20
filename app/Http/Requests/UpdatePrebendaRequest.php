<?php

namespace App\Http\Requests;


use App\Calculators\PrebendasClerigos\MaxPrebendasClerigoCalculator;
use App\Models\PessoasPrebenda;
use App\Rules\TakeMaxPrebendaForAnoAndFuncaoMinisterial;
use App\Traits\Identifiable;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePrebendaRequest extends FormRequest
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
            'ano' => [
                'required',
                function ($attribute, $value, $fail) {
                    $id = $this->route('id');
                    $prebendaDuplicada = PessoasPrebenda::where('ano', $value)
                        ->where('pessoa_id', Identifiable::fetchSessionPessoa()->id)
                        ->when($id, fn ($query) => $query->where('id', '<>', $id))
                        ->exists();

                    if ($prebendaDuplicada) {
                        $fail('Você já cadastrou a prebenda deste ano');
                    }
                },
            ],
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
