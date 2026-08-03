<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEbdLiderancaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'membro_id' => ['required', 'exists:membresia_membros,id'],
            'cargo' => ['required', Rule::in(['superintendente', 'secretario', 'tesoureiro'])],
            'ativo' => ['required', 'boolean'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
        ];
    }
}
