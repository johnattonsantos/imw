<?php

namespace App\Http\Requests;

use App\Rules\RangeDateRule;
use App\Rules\UniqueCPFInIgrejaRule;
use App\Rules\UniqueRolIgrejaRule;
use App\Rules\ValidaCPF;
use App\Models\PessoaStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreReceberNovoClerigoRequest extends FormRequest
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
        $cpfRules = ['nullable', 'max:18', new ValidaCPF];

        if (!$this->situacaoDispensaCpf()) {
            array_unshift($cpfRules, 'required');
        }

        return [
            'nome' => 'required|max:255',
            'identidade' => ['required', 'max:13', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d$/'],
            'identidade_uf' => 'required',
            'orgao_emissor' => 'required|max:50',
            'data_emissao' => ['required', 'date', new RangeDateRule],
            'cpf' => $cpfRules,
            'situacao' => 'required',
            'endereco' => 'required|max:255',
            'numero' => 'required',
            'complemento' => 'nullable|max:50',
            'bairro' => 'required|max:100',
            'cidade' => 'required|max:100',
            'uf' => 'required|max:2',
            'pais' => 'nullable',
            'cep' => 'required|min:8',
            'email' => 'required|email|max:255',
            'estado_civil' => 'required|max:20',
            'sexo' => 'required|in:M,F',
            'nome_mae' => 'nullable|max:255',
            'nome_pai' => 'nullable|max:255',
            'telefone_preferencial' => 'required|max:15',
            'telefone_alternativo' => 'nullable',
            'ctps' => 'max:20|nullable',
            'ctps_emissao' => ['nullable', new RangeDateRule],
            'titulo_eleitor' => 'nullable|max:20',
            'titulo_eleitor_secao' => 'nullable|max:10',
            'titulo_eleitor_zona' => 'nullable|max:10',
            'formacao_id' => 'required',
            'categoria' => 'required',
            'situacao' => 'required',
            'habilitacao' => 'nullable',
            'habilitacao_categoria' =>  'nullable',
            'habilitacao_emissor' => 'nullable',
            'habilitacao_uf' => 'nullable',
            'data_nascimento' => ['required', new RangeDateRule],
            'pispasep' => 'nullable',
            'data_consagracao' => ['required', new RangeDateRule],
            'data_ordenacao' => ['nullable', new RangeDateRule],
            'data_integralizacao' => ['nullable', new RangeDateRule],
            'rol' => 'required',

        ];
    }

    private function situacaoDispensaCpf(): bool
    {
        $situacaoId = $this->input('situacao');

        if (!$situacaoId) {
            return false;
        }

        $situacao = PessoaStatus::find($situacaoId);

        return $situacao ? $situacao->cpfOpcional() : false;
    }

    public function messages()
    {
        return [
            'tipo.required' => 'O campo tipo é obrigatório.',
            'nome.required' => 'O campo nome é obrigatório.',
            'identidade.required' => 'O campo identidade é obrigatório.',
            'identidade.max' => 'O campo identidade deve conter no máximo 10 dígitos.',
            'identidade.regex' => 'O campo identidade deve estar no formato 123.456.789-0.',
            'identidade_uf.required' => 'O campo estado em identidade é obrigatório.',
            'orgao_emissor.required' => 'O campo órgão emissor é obrigatório.',
            'data_emissao.required' => 'O campo data de emissão é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'situacao.required' => 'O campo situação é obrigatório.',
            'situacao.required' => 'O campo situção é obrigatório.',
            'endereco.required' => 'O campo endereço é obrigatório.',
            'numero.required' => 'O campo número é obrigatório.',
            'bairro.required' => 'O campo bairro é obrigatório.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'data_nascimento.required' => 'O campo data de nascimento é obrigatório.',
            'uf.required' => 'O campo estado é obrigatório.',

            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.min' => 'O campo CEP deve conter pelo menos 8 caracteres..',
            'email.required' => 'O campo email é obrigatório.',
            'estado_civil.required' => 'O campo estado civil é obrigatório.',
            'regiao_id.required' => 'O campo região é obrigatório.',
            'sexo.required' => 'O campo sexo é obrigatório.',
            'escolaridade.required' => 'O campo escolaridade é obrigatório.',
            'telefone_preferencial.required' => 'O campo celular é obrigatório.',
            'titulo_eleitor.required' => 'O campo título de eleitor é obrigatório.',
            'titulo_eleitor_secao.required' => 'O campo seção do título de eleitor é obrigatório.',
            'titulo_eleitor_zona.required' => 'O campo zona do título de eleitor é obrigatório.',
            'formacao_id.required' => 'O campo formação é obrigatório.',
            'data_consagracao.required' => 'O campo data de consagração é obrigatório.',
            'rol.required' => 'O campo rol é obrigatório.',
        ];
    }
    public function attributes()
    {
        return [
            'telefone_preferencial' => 'celular',
        ];
    }
}
