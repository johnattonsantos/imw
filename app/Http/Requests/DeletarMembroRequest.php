<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeletarMembroRequest extends FormRequest
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
            'modo_exclusao_id' => 'required|exists:membresia_situacoes,id',
            'dt_exclusao'      => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'modo_exclusao_id.required' => 'Você precisa preencher esta informação.',
            'dt_exclusao.required'      => 'Você precisa preencher esta informação.',
        ];
    }
}
