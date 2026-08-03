<?php

namespace App\Http\Requests\Patrimonio;

use App\Models\Patrimonio\BemMovel;
use App\Models\Patrimonio\Imovel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatrimonioDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:180'],
            'tipo' => ['required', 'string', 'max:120'],
            'arquivo' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'mimetypes:application/pdf,image/jpeg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'data_emissao' => ['nullable', 'date'],
            'data_validade' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:60'],
            'observacoes' => ['nullable', 'string'],
            'documentavel_type' => ['required', Rule::in(['imovel', 'bem_movel'])],
            'documentavel_id' => ['required', 'integer', function (string $attribute, mixed $value, \Closure $fail) {
                $type = (string) $this->input('documentavel_type');

                if ($type === 'imovel' && ! Imovel::query()->whereKey($value)->exists()) {
                    $fail('Imóvel selecionado não foi encontrado.');
                }

                if ($type === 'bem_movel' && ! BemMovel::query()->whereKey($value)->exists()) {
                    $fail('Bem móvel selecionado não foi encontrado.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'arquivo.mimetypes' => 'Tipo de arquivo inválido. Envie PDF, JPG, PNG, DOC ou DOCX.',
            'arquivo.max' => 'O arquivo deve ter no máximo 20MB.',
            'nome.required' => 'O nome do documento é obrigatório.',
            'tipo.required' => 'O tipo do documento é obrigatório.',
            'documentavel_type.required' => 'Selecione o tipo de vínculo do documento.',
            'documentavel_id.required' => 'Selecione o item vinculado ao documento.',
        ];
    }
}
