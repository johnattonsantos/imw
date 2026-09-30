<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferenciaCongregadoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dt_notificacao' => 'required|date|before_or_equal:today',
            'igreja_id' => 'required|exists:instituicoes_instituicoes,id',
        ];
    }
}
