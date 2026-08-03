<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatrimonioBenfeitoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imovel_id' => ['required', 'integer', Rule::exists('patrimonio_imoveis', 'id')],
            'descricao' => ['required', 'string'],
            'data' => ['nullable', 'date'],
            'valor_investido' => ['required', 'numeric', 'min:0'],
            'responsavel' => ['nullable', 'string', 'max:180'],
            'documento_anexo' => [
                'nullable',
                'file',
                'max:20480',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'mimetypes:application/pdf,image/jpeg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'imovel_id.required' => 'Selecione o imóvel da benfeitoria.',
            'descricao.required' => 'A descrição da benfeitoria é obrigatória.',
            'valor_investido.required' => 'Informe o valor investido.',
            'documento_anexo.mimetypes' => 'Tipo de arquivo inválido. Envie PDF, JPG, PNG, DOC ou DOCX.',
            'documento_anexo.max' => 'O anexo deve ter no máximo 20MB.',
        ];
    }
}
