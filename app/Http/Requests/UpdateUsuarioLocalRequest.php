<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidaCPF;
use App\Models\User;

class UpdateUsuarioLocalRequest extends FormRequest
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

        $userId = $this->route('id');
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'perfil_id' => 'required',
           'cpf' => [
                'required',
                function ($attribute, $value, $fail) use ($userId) {
                    $cpfSemMascara = $this->removeMascaraCPF($value);
                    $user = User::where('cpf', $cpfSemMascara)->where('id', '!=', $userId)->first();
                    if ($user) {
                        $fail(__('O CPF já está sendo utilizado por outra pessoa.'));
                    }
                },
                new ValidaCPF,
            ],
            'telefone' => [
                'required',
                function ($attribute, $value, $fail) use ($userId) {
                    $telefoneSemMascara = $this->removeMascaraTelefone($value);
                    $user = User::where('telefone', $telefoneSemMascara)->where('id', '!=', $userId)->first();
                    if ($user) {
                        $fail(__('O telefone já está sendo utilizado por outra pessoa.'));
                    }
                },
                'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/',
                'min:10',
            ],
        ];
    }

    public function messages()
    {
        return [
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
