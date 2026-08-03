<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEbdDiarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'turma_id' => ['required', 'exists:ebd_turmas,id'],
            'data_aula' => ['required', 'date'],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'hora_fim' => ['nullable', 'date_format:H:i', 'after:hora_inicio'],
            'periodo_aula' => ['required', 'in:manha,noite'],
            'tema_aula' => ['required', 'string', 'max:160'],
            'conteudo' => ['required', 'string'],
            'observacoes' => ['nullable', 'string'],
            'presencas' => ['required', 'array', 'min:1'],
            'presencas.*.aluno_id' => ['required', 'integer', 'exists:ebd_alunos,id'],
            'presencas.*.presente' => ['nullable', 'boolean'],
            'presencas.*.justificativa' => ['nullable', 'string'],
        ];
    }
}
