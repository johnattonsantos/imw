<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidaCPF;
use App\Models\User;

class StoreUsuarioLocalRequest extends FormRequest
{
    // Métodos de autorização e outras funções...

    public function rules()
    {
        // Verificar se o tipo é 'cadastro' ou 'vinculo'
        $tipo = $this->input('tipo');

        // Definir as regras para o CPF
        $cpfRules = $tipo === 'cadastro' ? [
            'required',
            function ($attribute, $value, $fail) {
                $cpfSemMascara = $this->removeMascaraCPF($value);
                if (User::where('cpf', $cpfSemMascara)->exists()) {
                    $fail(__('O CPF já está sendo utilizado por outra pessoa.'));
                }
            },
            new ValidaCPF,
        ] : ['nullable'];

        // Definir as regras para o telefone
        $telefoneRules = $tipo === 'cadastro' ? [
            'required',
            function ($attribute, $value, $fail) {
                $telefoneSemMascara = $this->removeMascaraTelefone($value);
                if (User::where('telefone', $telefoneSemMascara)->exists()) {
                    $fail(__('O telefone já está sendo utilizado por outra pessoa.'));
                }
            },
            'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/',
            'min:10',
        ] : ['nullable'];

        // Definir as regras para a senha
        $passwordRules = $tipo === 'cadastro' ? ['required', 'string', 'min:6', 'confirmed'] : ['nullable'];

        // Definir as regras para o nome
        $nameRules = $tipo === 'cadastro' ? 'required|string|min:4' : 'nullable';

        return [
            'name' => $nameRules,
            'email_hidden' => 'required|email',
            'password' => $passwordRules,
            'perfil_id' => 'required',
            'cpf' => $cpfRules,
            'telefone' => $telefoneRules,
            'pessoa_id' => 'nullable'
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'O campo nome completo é obrigatório.',
            'name.min' => 'O campo nome completo deve ter no mínimo :min caracteres.',

            'email_hidden.required' => 'O campo e-mail é obrigatório.',
            'email_hidden.email' => 'Por favor, insira um endereço de e-mail válido.',

            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'O campo senha deve ter no mínimo :min caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',

            'perfil_id.required' => 'O campo perfil é obrigatório.',
        ];
    }

    function removeMascaraCPF($cpf)
    {
        return preg_replace('/\D/', '', $cpf);
    }

    function removeMascaraTelefone($telefone)
    {
        return preg_replace('/\D/', '', $telefone);
    }


}

