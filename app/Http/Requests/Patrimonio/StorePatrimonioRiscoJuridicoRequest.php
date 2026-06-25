<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatrimonioRiscoJuridicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imovel_id' => ['required', 'integer', Rule::exists('patrimonio_imoveis', 'id')],
            'possui_onus' => ['required', 'boolean'],
            'tipo_onus' => ['nullable', 'string', 'max:120', 'required_if:possui_onus,1'],
            'descricao' => ['required', 'string'],
            'nivel_risco' => ['required', Rule::in(['baixo', 'medio', 'alto', 'critico'])],
            'data_identificacao' => ['required', 'date'],
            'providencia_recomendada' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aberto', 'em_andamento', 'mitigado', 'encerrado'])],
        ];
    }

    public function messages(): array
    {
        return [
            'imovel_id.required' => 'Selecione o imóvel relacionado ao risco.',
            'descricao.required' => 'A descrição do risco jurídico é obrigatória.',
            'nivel_risco.required' => 'Informe o nível de risco.',
            'data_identificacao.required' => 'Informe a data de identificação do risco.',
            'status.required' => 'Informe o status do risco jurídico.',
        ];
    }
}
