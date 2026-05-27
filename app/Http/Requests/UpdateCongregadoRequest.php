<?php

namespace App\Http\Requests;

use App\Rules\RangeDateRule;
use App\Rules\ValidaCPF;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCongregadoRequest extends FormRequest
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
        $membroId = $this->input('membro_id');
        $dataNascimento = $this->input('data_nascimento');
        $minDate = '1910-01-01';
        $currentDate = date('Y-m-d');

        return [
            'foto' => 'image|nullable|max:10240',
            'nome' => 'required',
            'sexo' => 'required',
            'data_nascimento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($minDate, $currentDate) {
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de nascimento deve estar entre 01/01/1910 e a data atual.');
                    }
                },
            ],
            'data_conversao' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de conversão deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de conversão deve ser após a data de nascimento e a data atual.');
                    }
                },
            ],
            'data_batismo' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de batismo deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de batismo deve ser após a data de nascimento e a data atual.');
                    }
                },
            ],
            'data_batismo_espirito' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de batismo no Espírito deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de batismo no Espírito deve ser após a data de nascimento e a data atual.');
                    }
                },
            ],
            'estado_civil' => 'required',
            'nacionalidade' => 'required',
            'naturalidade' => 'required',
            'uf' => 'required',
            'cpf' => [
                'nullable',
                Rule::unique('membresia_membros', 'cpf')->ignore($membroId),
                new ValidaCPF,
            ],
            'email_preferencial' => ['nullable', 'email', function ($attribute, $value, $fail) {
                if ($value) {
                    if (!preg_match('/@.*\.\w{2,}$/', $value)) {
                        $fail('O campo e-mail deve conter um sufixo de domínio válido com pelo menos dois caracteres após o ponto.');
                    }
                }
            }],
            'email_alternativo' => 'email|nullable',
            'telefone_preferencial' => ['nullable', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', 'min:10'],
            'telefone_alternativo' => ['nullable', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', 'min:10'],
            'telefone_whatsapp' => ['nullable', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', 'min:10'],
            'data_casamento' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de casamento deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de casamento deve ser após a data de nascimento e a data atual.');
                    }
                },
            ],
            'congregacao_id' => 'nullable|exists:congregacoes_congregacoes,id'
        ];
    }

    public function messages()
    {
        return [
            'cpf.unique' => 'Este CPF já está sendo utilizado por outra pessoa'
        ];
    }
}
