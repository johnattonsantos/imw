<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEbdVisitanteRapidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100'],
            'sexo' => ['required', Rule::in(['M', 'F'])],
            'cpf' => ['nullable', 'string', 'max:14'],
            'telefone_preferencial' => ['nullable', 'string', 'max:20'],
            'email_preferencial' => ['nullable', 'email', 'max:255'],
            'data_nascimento' => ['nullable', 'date'],
        ];
    }
}
