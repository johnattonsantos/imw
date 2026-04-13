<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class StoreReceberNovoRequest extends FormRequest
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
            'regiao_id' => 'required',
            'bairro' => 'required',
            'cep' => 'required',
            'cidade' => 'required',
            'cnpj' => 'required',
            'complemento' => '',
            'data_abertura' => 'required|date|before_or_equal:today|after_or_equal:1967-01-05',
            'numero' => 'required',
            'pais' => 'required|String',
            'telefone' => 'required|max:11',
            'uf' => 'required',
            'endereco' => 'required',
            'ddd' => 'required|max:2',
            'ativo' => 'required|boolean',
            'data_encerramento' => [
                'nullable',
                Rule::requiredIf(fn () => (string) $this->input('ativo') === '0'),
                'date',
                'after:data_abertura',
                'after_or_equal:2023-01-01',
                'before_or_equal:today',
            ],
        ];
    }
    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'tipo_instituicao_id.required' => 'O tipo de instituição é obrigatório.',
            'instituicao_pai_id.required' => 'A instituição pai é obrigatório.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cep.required' => 'O CEP é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'data_abertura.required' => 'A data de abertura é obrigatória.',
            'data_abertura.date' => 'A data de abertura deve ser uma data válida.',
            'data_abertura.after_or_equal' => 'A data de abertura não pode ser anterior a 05/01/1967.',
            'numero.required' => 'O número é obrigatório.',
            'pais.required' => 'O país é obrigatório.',
            'pais.string' => 'O país deve ser uma string válida.',
            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.max' => 'O telefone não pode ter mais que 11 caracteres.',
            'uf.required' => 'O estado é obrigatório.',
            'uf.max' => 'O estado não pode ter mais que 2 caracteres.',
            'endereco.required' => 'O endereço é obrigatório.',
            'ddd.required' => 'O DDD é obrigatório.',
            'ativo.required' => 'O status é obrigatório.',
            'ativo.boolean' => 'O status informado é inválido.',
            'data_encerramento.required' => 'Para inativar a instituição, informe a data de encerramento.',
            'data_encerramento.date' => 'A data de encerramento deve ser uma data válida.',
            'data_encerramento.after' => 'A data de encerramento deve ser superior à data de criação.',
            'data_encerramento.after_or_equal' => 'A data de encerramento não pode ser anterior a 01/01/2023.',
            'data_encerramento.before_or_equal' => 'A data de encerramento não pode ser posterior à data atual.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ((string) $this->input('ativo') !== '0') {
                return;
            }

            if (!$this->filled('data_abertura') || !$this->filled('data_encerramento')) {
                return;
            }

            try {
                $dataAbertura = Carbon::parse($this->input('data_abertura'))->startOfDay();
                $dataEncerramento = Carbon::parse($this->input('data_encerramento'))->startOfDay();
            } catch (\Throwable $e) {
                return;
            }

            if ($dataEncerramento->lessThanOrEqualTo($dataAbertura)) {
                $validator->errors()->add(
                    'data_encerramento',
                    'A data de encerramento deve ser maior que a data de abertura.'
                );
            }
        });
    }
}
