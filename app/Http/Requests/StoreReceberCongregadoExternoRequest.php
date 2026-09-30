<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceberCongregadoExternoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dt_resposta' => 'required|date|before_or_equal:today',
            'congregacao_id' => 'nullable|exists:congregacoes_congregacoes,id',
            'action' => 'required|in:accept,reject',
        ];
    }
}
