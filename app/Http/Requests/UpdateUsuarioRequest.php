<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidaCPF;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
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
            'cpf' => [
                'required',
                new ValidaCPF,
                function ($attribute, $value, $fail) use ($userId) {
                    $cpf = preg_replace('/\D/', '', (string) $value);
                    $user = User::where('cpf', $cpf)->where('id', '!=', $userId)->first();

                    if ($user) {
                        $fail(__('O CPF já está sendo utilizado por outra pessoa.'));
                    }
                },
            ],
            'perfil_id.*' => 'required|integer|exists:perfils,id',
            'instituicao_id.*' => 'required|integer|exists:instituicoes_instituicoes,id',
        ];
    }
}
