<?php

namespace App\Http\Requests;

use App\Rules\TodaysDeadlineRule;
use App\Rules\UniqueRolIgrejaRule;
use App\Rules\ValidaCPF;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateMembroRequest extends FormRequest
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
        $membroId = $this->input('membro_id');
        $isRecadastramento = $this->routeIs('recadastramento-membro.update');
        $dataNascimento = $this->input('data_nascimento');
        $minDate = '1910-01-01';
        $minDateRecepcao = '1967-01-05';
        $currentDate = date('Y-m-d');

        return [
            'foto' => 'image|nullable|max:1999',
            'nome' => 'required',
            'sexo' => 'required',
            'data_nascimento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($minDate, $currentDate) {
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de nascimento deve estar entre 01/01/1910 e a data atual.');
                    }
                },
            ],
            'data_conversao' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de conversão deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de conversão deve ser após a data de nascimento e a data atual.');
                    }
                },
            ],
            'data_batismo' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de batismo deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de batismo deve ser após a data de nascimento e a data atual.');
                    }
                },
            ],
            'data_batismo_espirito' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de batismo no Espírito deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de batismo no Espírito deve ser após a data de nascimento e a data atual.');
                    }
                },

            ],
            'dt_recepcao' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDateRecepcao, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de recepção deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDateRecepcao) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de recepção deve estar entre 05/01/1967 e a data atual.');
                    }
                },
                new TodaysDeadlineRule
            ],
            'modo_recepcao_id' => 'nullable|exists:membresia_situacoes,id',
            'dt_exclusao' => [
                'nullable',
                'date',
                $isRecadastramento ? 'required_if:status,I' : 'nullable',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (empty($value)) {
                        return;
                    }

                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de exclusão deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de exclusão deve ser após a data de nascimento e a data atual.');
                    }
                },
                function ($attribute, $value, $fail) use ($currentDate) {
                    if ($this->input('status') !== 'I') {
                        return;
                    }

                    if (empty($value)) {
                        return;
                    }

                    $dtRecepcao = $this->input('dt_recepcao');
                    if (empty($dtRecepcao)) {
                        $fail('Para status Inativo, informe também a data de recepção.');
                        return;
                    }

                    if (strtotime($value) < strtotime($dtRecepcao) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de exclusão deve estar entre a data de recepção e a data atual.');
                    }
                },
            ],
            'modo_exclusao_id' => $isRecadastramento
                ? 'nullable|exists:membresia_situacoes,id|required_if:status,I'
                : 'nullable|exists:membresia_situacoes,id',
            'estado_civil' => 'required',
            'nacionalidade' => 'required',
            'naturalidade' => 'required',
            'status' => $isRecadastramento ? 'required|in:A,I' : 'nullable|in:A,I',
            'uf' => 'sometimes|required',
            'rol_atual' => [
                'required',
                'integer',
                'min:1',
                new UniqueRolIgrejaRule($membroId),
            ],
            'cpf' => [
                'required',
                new ValidaCPF,
                function ($attribute, $value, $fail) use ($membroId) {
                    // Remove todos os caracteres que não são números
                    $cpf = preg_replace('/[^0-9]/', '', $value);

                    // Verifica se o CPF já existe na tabela membresia_membros, ignorando o membro atual
                    $query = DB::table('membresia_membros')->where('cpf', $cpf);

                    if ($membroId) {
                        $query->where('id', '!=', $membroId);
                    }

                    if ($query->exists()) {
                        $fail('Este CPF já está sendo utilizado por outra pessoa');
                    }
                },
            ],
            'email_preferencial' => ['nullable', 'email', function ($attribute, $value, $fail) {
                if ($value) {
                    if (!preg_match('/@.*\.\w{2,}$/', $value)) {
                        $fail('O campo e-mail deve conter um sufixo de domínio válido com pelo menos dois caracteres após o ponto.');
                    }
                }
            }],
            'email_alternativo' => 'email|nullable',
            'telefone_preferencial' => [
                $isRecadastramento ? 'required' : 'nullable',
                'regex:/^(\+\d{2}\s?)?\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/',
                'min:10'
            ],
            'telefone_alternativo' => ['nullable', 'regex:/^(\+\d{2}\s?)?\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', 'min:10'],
            'telefone_whatsapp' => ['nullable', 'regex:/^(\+\d{2}\s?)?\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', 'min:10'],
            'cep' => $isRecadastramento ? 'required' : 'nullable',
            'endereco' => $isRecadastramento ? 'required' : 'nullable',
            'numero' => $isRecadastramento ? 'required' : 'nullable',
            'bairro' => $isRecadastramento ? 'required' : 'nullable',
            'cidade' => $isRecadastramento ? 'required' : 'nullable',
            'estado' => $isRecadastramento ? 'required' : 'nullable',
            'data_casamento' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail('A data de casamento deve ser após a data de nascimento.');
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail('A data de casamento deve ser após a data de nascimento e a data atual.');
                    }
                },
            ],
            'congregacao_id' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'rol_atual.required' => 'O campo Nº Rol é obrigatório.',
            'rol_atual.integer' => 'O campo Nº Rol deve conter apenas números.',
            'rol_atual.min' => 'O campo Nº Rol deve ser maior que zero.',
            'status.required' => 'O campo Status é obrigatório.',
            'status.in' => 'O campo Status deve ser Ativo ou Inativo.',
            'dt_exclusao.required_if' => 'Para status Inativo, a data de exclusão é obrigatória.',
            'modo_exclusao_id.required_if' => 'Para status Inativo, o modo de exclusão é obrigatório.',
            'telefone_preferencial.required' => 'O campo Telefone é obrigatório.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'endereco.required' => 'O campo Endereço é obrigatório.',
            'numero.required' => 'O campo Número é obrigatório.',
            'bairro.required' => 'O campo Bairro é obrigatório.',
            'cidade.required' => 'O campo Cidade é obrigatório.',
            'estado.required' => 'O campo Estado é obrigatório.',
        ];
    }
}
