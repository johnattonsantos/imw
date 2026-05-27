<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinanceiroStoreNewAnexoRequest extends FormRequest
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
            'anexo'           => 'required|file|max:10240|mimes:jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx',
            'descricao_anexo' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'anexo.required'      => 'Você deve anexar um arquivo.',
            'anexo.max'           => 'O tamanho do arquivo deve ter no máximo 10MB.',
            'anexo.mimes'         => 'O arquivo deve ser um arquivo PDF, DOC ou DOCX.',
            'descricao_anexo.max' => 'A descrição do anexo deve ter no máximo 255 caracteres.',
        ];
    }
}
