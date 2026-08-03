<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEbdClasseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:120'],
            'faixa_etaria' => ['nullable', 'string', 'max:120'],
            'descricao' => ['nullable', 'string'],
            'ativo' => ['required', 'boolean'],
        ];
    }
}
