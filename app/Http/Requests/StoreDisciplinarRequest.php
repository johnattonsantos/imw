<?php

namespace App\Http\Requests;

use App\Rules\DatePreviousToRecebimentoRule;
use App\Rules\RangeDateRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDisciplinarRequest extends FormRequest
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
        $membroId = $this->route('id');

        return [
            'dt_inicio' => [
                'required',
                'date',
                new RangeDateRule, 
                new DatePreviousToRecebimentoRule($membroId)
            ],
            'dt_termino' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && $this->input('dt_inicio')) {
                        $dtInicio = strtotime($this->input('dt_inicio'));
                        $dtTermino = strtotime($value);
                        if ($dtTermino < $dtInicio) {
                            $fail(__('A data de término não pode ser anterior à data de início.'));
                        }
                    }
                }
            ],
            'modo_exclusao_id' => 'required',
            'clerigo_id' => 'required',
        ];
    }
}
