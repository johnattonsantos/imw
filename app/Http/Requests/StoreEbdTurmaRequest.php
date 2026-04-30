<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEbdTurmaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classe_id' => ['required', 'exists:ebd_classes,id'],
            'professor_id' => ['required', 'exists:ebd_professores,id'],
            'nome' => ['required', 'string', 'max:120'],
            'ano' => ['required', 'integer', 'min:2000', 'max:2100'],
            'semestre' => ['nullable', 'integer', 'in:1,2'],
            'ativo' => ['required', 'boolean'],
            'alunos' => ['nullable', 'array'],
            'alunos.*' => ['integer', 'exists:ebd_alunos,id'],
        ];
    }
}
