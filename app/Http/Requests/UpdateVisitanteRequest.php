<?php

namespace App\Http\Requests;

use App\Rules\RangeDateRule;
use App\Rules\VisitanteExistenteRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitanteRequest extends FormRequest
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
        $dataNascimento = $this->input('data_nascimento');
        $minDate = '1910-01-01';
        $currentDate = date('Y-m-d');

        return [
            'nome' => 'required',
            'sexo' => 'required',
            'data_nascimento' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($minDate, $currentDate) {
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de nascimento deve estar entre 01/01/1910 e a data atual.'));
                    }
                },
            ],
            'data_conversao' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail(__('A data de conversão deve ser após a data de nascimento.'));
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de conversão deve ser após a data de nascimento e a data atual.'));
                    }
                },
            ],
            'email_preferencial' => ['nullable', 'email', function ($attribute, $value, $fail) {
                if ($value) {
                    if (!preg_match('/@.*\.\w{2,}$/', $value)) {
                        $fail(__('O campo e-mail deve conter um sufixo de domínio válido com pelo menos dois caracteres após o ponto.'));
                    }
                }
            }],
            'email_alternativo' => 'email|nullable',
            'telefone_preferencial' => 'nullable|string|min:10',
            'telefone_alternativo' => 'nullable|string|min:10',
            'telefone_whatsapp' => 'nullable|string|min:10',
            'congregacao_id' => 'nullable|exists:congregacoes_congregacoes,id'
        ];
    }
}
