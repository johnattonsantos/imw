<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatrimonioBemMovelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imovel_id' => ['nullable', 'integer', 'exists:patrimonio_imoveis,id'],
            'placa_patrimonial' => ['nullable', 'string', 'max:60'],
            'nome' => ['required', 'string', 'max:180'],
            'categoria' => ['nullable', 'string', 'max:120'],
            'descricao' => ['nullable', 'string'],
            'estado_conservacao' => ['nullable', 'string', 'max:60'],
            'localizacao' => ['nullable', 'string', 'max:180'],
            'responsavel' => ['nullable', 'string', 'max:180'],
            'data_aquisicao' => ['nullable', 'date'],
            'valor_aquisicao' => ['nullable', 'numeric', 'min:0'],
            'valor_residual' => ['nullable', 'numeric', 'min:0'],
            'vida_util' => ['nullable', 'integer', 'min:0'],
            'natureza_comprobatoria' => ['nullable', 'string', 'max:120'],
            'numero_documento' => ['nullable', 'string', 'max:120'],
            'fornecedor_doador' => ['nullable', 'string', 'max:180'],
            'status' => ['required', Rule::in(['ativo', 'inativo', 'baixado', 'em_manutencao', 'depreciado'])],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do bem móvel é obrigatório.',
            'status.required' => 'O status do bem móvel é obrigatório.',
            'status.in' => 'Status inválido para o bem móvel.',
        ];
    }
}
