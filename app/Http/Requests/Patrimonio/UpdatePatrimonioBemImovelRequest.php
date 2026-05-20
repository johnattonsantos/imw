<?php

namespace App\Http\Requests\Patrimonio;

use App\Models\Patrimonio\Imovel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatrimonioBemImovelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Imovel|null $imovel */
        $imovel = $this->route('bemImovel');

        return [
            'codigo_patrimonial' => [
                'nullable',
                'string',
                'max:60',
                Rule::unique('patrimonio_imoveis', 'codigo_patrimonial')->ignore($imovel?->id),
            ],
            'natureza_imovel' => ['nullable', 'string', 'max:120'],
            'nome' => ['required', 'string', 'max:180'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:120'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:9'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'area_total' => ['nullable', 'numeric', 'min:0'],
            'area_construida' => ['nullable', 'numeric', 'min:0'],
            'iptu_itr' => ['nullable', 'string', 'max:120'],
            'inscricao_municipal_rural' => ['nullable', 'string', 'max:180'],
            'valor_historico' => ['nullable', 'numeric', 'min:0'],
            'valor_venal' => ['nullable', 'numeric', 'min:0'],
            'valor_mercado' => ['nullable', 'numeric', 'min:0'],
            'situacao_tributaria' => ['nullable', 'string', 'max:120'],
            'status_titularidade' => ['nullable', 'string', 'max:80'],
            'numero_matricula' => ['nullable', 'string', 'max:120'],
            'cartorio' => ['nullable', 'string', 'max:180'],
            'tipo_titulo' => ['nullable', 'string', 'max:120'],
            'data_aquisicao_posse' => ['nullable', 'date'],
            'possui_escritura_registrada' => ['nullable', 'boolean'],
            'observacoes_juridicas' => ['nullable', 'string'],
            'avcb_validade' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do imóvel é obrigatório.',
            'estado.size' => 'O estado deve conter 2 caracteres (UF).',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'codigo_patrimonial.unique' => 'Este código patrimonial já está em uso.',
        ];
    }
}
