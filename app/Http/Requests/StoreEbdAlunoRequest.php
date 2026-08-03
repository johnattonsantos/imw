<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEbdAlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'membro_id' => ['required', 'exists:membresia_membros,id'],
            'ativo' => ['required', 'boolean'],
            'observacoes' => ['nullable', 'string'],
        ];
    }
}
