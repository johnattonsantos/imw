<?php

namespace App\Http\Requests;

use App\Support\CpfCnpj;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFornecedorRequest extends FormRequest
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
        return [
            'cpf_cnpj' => [
                'required',
                'string',
                'max:18',
                function ($attribute, $value, $fail) {
                    if (!CpfCnpj::isValidCpfOrCnpj($value)) {
                        $fail('O CPF/CNPJ informado é inválido. Para CNPJ alfanumérico, use 12 letras/números e 2 dígitos verificadores numéricos.');
                    }
                },
            ],
            'nome' => 'required|max:100',
            'email' => ['nullable', 'email', function ($attribute, $value, $fail) {
                if ($value) {
                    if (!preg_match('/@.*\.\w{2,}$/', $value)) {
                        $fail('O campo e-mail deve conter um sufixo de domínio válido com pelo menos dois caracteres após o ponto.');
                    }
                }
            }],
            'site' => 'nullable|max:100',
            'cep' => 'required|numeric',
            'endereco' => 'nullable|max:255',
            'numero' => 'nullable|max:20',
            'complemento' => 'nullable|max:255',
            'bairro' => 'nullable|max:255',
            'cidade' => 'nullable|max:255',
            'uf' => 'nullable|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
            'pais' => 'required|max:20',
            'telefone' => 'nullable|max:20',
            'celular' => 'required|max:20',
        ];
    }
}
