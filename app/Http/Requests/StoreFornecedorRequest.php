<?php

namespace App\Http\Requests;

use App\Support\CpfCnpj;
use Illuminate\Foundation\Http\FormRequest;

class StoreFornecedorRequest extends FormRequest
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
            'nome' => 'required|string|max:100',
            'email' => ['nullable', 'email', function ($attribute, $value, $fail) {
                if ($value) {
                    if (!preg_match('/@.*\.\w{2,}$/', $value)) {
                        $fail('O campo e-mail deve conter um sufixo de domínio válido com pelo menos dois caracteres após o ponto.');
                    }
                }
            }],
            'site' => 'nullable|string|max:100',
            'cep' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'pais' => 'required|string|max:20',
            'telefone' => 'nullable|string|min:10',
            'celular' => 'required|string|min:10',
        ];
    }

    public function messages()
    {
        return [
            'cpf_cnpj.required' => 'O CPF/CNPJ é obrigatório.',
            'cpf_cnpj.max' => 'O CPF/CNPJ não pode ter mais de 18 caracteres.',
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres.',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais de 100 caracteres.',
            'site.max' => 'O site não pode ter mais de 100 caracteres.',
            'cep.max' => 'O CEP não pode ter mais de 20 caracteres.',
            'endereco.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'numero.max' => 'O número não pode ter mais de 20 caracteres.',
            'complemento.max' => 'O complemento não pode ter mais de 255 caracteres.',
            'bairro.max' => 'O bairro não pode ter mais de 255 caracteres.',
            'cidade.max' => 'A cidade não pode ter mais de 255 caracteres.',
            'estado.max' => 'O estado não pode ter mais de 2 caracteres.',
            'pais.required' => 'O país é obrigatório.',
            'pais.max' => 'O país não pode ter mais de 20 caracteres.',
            'telefone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'celular.required' => 'O celular é obrigatório.',
            'celular.max' => 'O celular não pode ter mais de 20 caracteres.',
        ];
    }
}
