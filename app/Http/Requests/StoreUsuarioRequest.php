<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidaCPF;
use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
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
        return [
            'name' => 'required|string|min:4',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'cpf' => [
                'required',
                new ValidaCPF,
                function ($attribute, $value, $fail) {
                    $cpf = preg_replace('/\D/', '', (string) $value);

                    if (User::where('cpf', $cpf)->exists()) {
                        $fail(__('O CPF já está sendo utilizado por outra pessoa.'));
                    }
                },
            ],
            'perfil_id.*' => 'required',
            'instituicao_id.*' => 'required',
            'pessoa_id' => 'nullable'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'O campo Nome completo é obrigatório.',
            'name.min' => 'O campo Nome completo deve ter no mínimo :min caracteres.',

            'email.required' => 'O campo E-mail é obrigatório.',
            'email.email' => 'Por favor, insira um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',

            'password.required' => 'O campo Senha é obrigatório.',
            'password.min' => 'O campo Senha deve ter no mínimo :min caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',

            'cpf.required' => 'O campo CPF é obrigatório.',

            'perfil_id.*.required' => 'O campo Perfil é obrigatório.',

            'instituicao_id.*.required' => 'O campo Instituição é obrigatório.',
        ];
    }
}
