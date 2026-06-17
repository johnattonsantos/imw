<?php

namespace App\Http\Requests;

use App\Support\CpfCnpj;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSecretariaRequest extends FormRequest
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
            'nome' => 'required',
            'tipo_instituicao_id' => 'required',
            'instituicao_pai_id' => 'required',
            'bairro' => 'required',
            'cep' => 'required',
            'cidade' => 'required',
            'cnpj' => [
                'required',
                'string',
                'max:18',
                function ($attribute, $value, $fail) {
                    if (! CpfCnpj::isValidCnpj($value)) {
                        $fail('O CNPJ informado é inválido. Para CNPJ alfanumérico, use 12 letras/números e 2 dígitos verificadores numéricos.');
                    }
                },
            ],
            'complemento' => '',
            'data_abertura' => 'required|date',
            'numero' => 'required',
            'pais' => 'required|String',
            'telefone' => 'required|max:11',
            'uf' => 'required',
            'endereco' => 'required',
            'ddd' => ''
        ];
    }
    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'tipo_instituicao_id.required' => 'O tipo de instituição é obrigatório.',
            'instituicao_pai_id.required' => 'O ID da instituição pai é obrigatório.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cep.required' => 'O CEP é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.max' => 'O CNPJ não pode ter mais de 18 caracteres.',
            'data_abertura.required' => 'A data de abertura é obrigatória.',
            'data_abertura.date' => 'A data de abertura deve ser uma data válida.',
            'numero.required' => 'O número é obrigatório.',
            'pais.required' => 'O país é obrigatório.',
            'pais.string' => 'O país deve ser uma string válida.',
            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.max' => 'O telefone não pode ter mais que 11 caracteres.',
            'uf.required' => 'O estado é obrigatório.',
            'uf.max' => 'O estado não pode ter mais que 2 caracteres.',
            'endereco.required' => 'O endereço é obrigatório.',
        ];
    }
}
