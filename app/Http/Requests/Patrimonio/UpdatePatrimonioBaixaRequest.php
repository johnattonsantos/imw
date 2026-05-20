<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatrimonioBaixaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bem_movel_id' => ['required', 'integer', Rule::exists('patrimonio_bens_moveis', 'id')],
            'motivo' => ['required', 'string', 'max:180'],
            'data_baixa' => ['required', 'date'],
            'responsavel' => ['nullable', 'string', 'max:180'],
            'documento_comprobatorio' => [
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
            'bem_movel_id.required' => 'Selecione o bem móvel para registrar a baixa.',
            'motivo.required' => 'Informe o motivo da baixa patrimonial.',
            'data_baixa.required' => 'Informe a data da baixa.',
            'documento_comprobatorio.mimetypes' => 'Tipo de arquivo inválido. Envie PDF, JPG, PNG, DOC ou DOCX.',
            'documento_comprobatorio.max' => 'O arquivo deve ter no máximo 20MB.',
        ];
    }
}
