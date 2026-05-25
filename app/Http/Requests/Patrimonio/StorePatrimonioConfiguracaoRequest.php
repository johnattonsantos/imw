<?php

namespace App\Http\Requests\Patrimonio;

use App\Http\Controllers\Patrimonio\PatrimonioConfiguracoesController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatrimonioConfiguracaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipo = (string) $this->route('tipo');
        $igrejaId = (int) (
            data_get(session('session_perfil'), 'instituicoes.igrejaLocal.id')
            ?? data_get(session('session_perfil'), 'instituicao_id')
            ?? 0
        );

        return [
            'nome' => [
                'required',
                'string',
                'max:180',
                Rule::unique('patrimonio_configuracoes', 'nome')
                    ->where(fn ($query) => $query->where('igreja_id', $igrejaId)->where('tipo', $tipo)),
            ],
            'descricao' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
            'ordem' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'tipo' => ['nullable', Rule::in(PatrimonioConfiguracoesController::tiposPermitidos())],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da configuração é obrigatório.',
            'nome.unique' => 'Já existe uma configuração com esse nome para este tipo.',
            'ordem.integer' => 'A ordem deve ser um número inteiro.',
            'ordem.min' => 'A ordem não pode ser negativa.',
        ];
    }
}
