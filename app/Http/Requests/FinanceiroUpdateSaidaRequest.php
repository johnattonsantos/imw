<?php

namespace App\Http\Requests;

use App\Rules\TodaysDeadlineRule;
use Illuminate\Foundation\Http\FormRequest;

class FinanceiroUpdateSaidaRequest extends FormRequest
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
            'plano_conta_id' => 'required',
            'caixa_id'       => 'required',
            'valor'          => 'required',
            'data_movimento' =>  ['required', new TodaysDeadlineRule],
            'descricao'      => 'nullable|string',
            'tipo_pagante_favorecido_id' => 'required',
            'anexo1' => 'nullable|file|max:10240|mimes:jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx',
            'descricao_anexo1' => 'nullable|string|max:255',
            'anexo2' => 'nullable|file|max:10240|mimes:jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx',
            'descricao_anexo2' => 'nullable|string|max:255',
            'anexo3' => 'nullable|file|max:10240|mimes:jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx',
            'descricao_anexo3' => 'nullable|string|max:255',
        ];
    }
}
