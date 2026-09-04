<?php

namespace App\Http\Requests;

use App\Rules\TodaysDeadlineRule;
use App\Rules\UniqueRolIgrejaRule;
use App\Rules\ValidaCPF;
use App\Services\ServiceMembros\ConsultaCpfMembroService;
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

    protected function prepareForValidation()
    {
        if (!$this->routeIs('recadastramento-membro.update')) {
            return;
        }

        $rolAtual = $this->input('rol_atual');
        if ($rolAtual === null) {
            return;
        }

        $rolNormalizado = is_string($rolAtual) ? trim($rolAtual) : $rolAtual;
        if ($rolNormalizado === '' || (is_numeric($rolNormalizado) && (int) $rolNormalizado === 0)) {
            $this->merge(['rol_atual' => null]);
        }
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
        $cpf = preg_replace('/[^0-9]/', '', $this->input('cpf', ''));
        $membroIdRegraRol = $membroId;
        $igrejaRecadastramentoId = null;

        if ($isRecadastramento && $cpf !== '') {
            $igrejaRecadastramentoId = DB::table('membresia_migracao')
                ->where('id', $membroId)
                ->value('igreja_id');
            $membroOficialId = DB::table('membresia_membros')
                ->where('cpf', $cpf)
                ->where('vinculo', 'M')
                ->orderByRaw("CASE WHEN status = 'A' AND deleted_at IS NULL THEN 0 ELSE 1 END")
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->value('id');
            if (!empty($membroOficialId)) {
                $membroIdRegraRol = $membroOficialId;
            }
        }
        $dataNascimento = $this->input('data_nascimento');
        $minDate = '1910-01-01';
        $minDateRecepcao = '1967-01-05';
        $currentDate = date('Y-m-d');
        $maxBirthDateForMembro = date('Y-m-d', strtotime('-10 years'));
        $mensagemIdadeMinimaMembro = 'Por questão de excepcionalidade, nenhuma criança deverá ser cadastrada como membro, com menos de 10 anos.';

        return [
            'foto' => 'image|nullable|max:10240',
            'nome' => 'required',
            'sexo' => 'required',
            'data_nascimento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($minDate, $currentDate, $maxBirthDateForMembro, $mensagemIdadeMinimaMembro) {
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de nascimento deve estar entre 01/01/1910 e a data atual.'));
                        return;
                    }

                    if (strtotime($value) > strtotime($maxBirthDateForMembro)) {
                        $fail(__($mensagemIdadeMinimaMembro));
                        return;
                    }
                },
            ],
            'data_conversao' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail(__('A data de conversão deve ser após a data de nascimento.'));
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de conversão deve ser após a data de nascimento e a data atual.'));
                    }
                },
            ],
            'data_batismo' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate, $mensagemIdadeMinimaMembro) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail(__('A data de batismo deve ser após a data de nascimento.'));
                        return;
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de batismo deve ser após a data de nascimento e a data atual.'));
                        return;
                    }
                    if (
                        !empty($dataNascimento) &&
                        strtotime($dataNascimento) <= strtotime('-10 years') &&
                        strtotime($value) < strtotime($dataNascimento . ' +10 years')
                    ) {
                        $fail(__($mensagemIdadeMinimaMembro));
                        return;
                    }
                },
            ],
            'data_batismo_espirito' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail(__('A data de batismo no Espírito deve ser após a data de nascimento.'));
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de batismo no Espírito deve ser após a data de nascimento e a data atual.'));
                    }
                },

            ],
            'dt_recepcao' => [
                $isRecadastramento ? 'required' : 'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDateRecepcao, $currentDate) {
                    if (empty($value)) {
                        return;
                    }
                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail(__('A data de recepção deve ser após a data de nascimento.'));
                    }
                    if (strtotime($value) < strtotime($minDateRecepcao) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de recepção deve estar entre 05/01/1967 e a data atual.'));
                    }
                },
                new TodaysDeadlineRule
            ],
            'modo_recepcao_id' => $isRecadastramento
                ? 'required|exists:membresia_situacoes,id'
                : 'nullable|exists:membresia_situacoes,id',
            'dt_exclusao' => [
                'nullable',
                'date',
                $isRecadastramento ? 'required_if:status,I' : 'nullable',
                function ($attribute, $value, $fail) use ($dataNascimento, $minDate, $currentDate) {
                    if (empty($value)) {
                        return;
                    }

                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail(__('A data de exclusão deve ser após a data de nascimento.'));
                    }
                    if (strtotime($value) < strtotime($minDate) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de exclusão deve ser após a data de nascimento e a data atual.'));
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
                        $fail(__('Para status Inativo, informe também a data de recepção.'));
                        return;
                    }

                    if (strtotime($value) < strtotime($dtRecepcao) || strtotime($value) > strtotime($currentDate)) {
                        $fail(__('A data de exclusão deve estar entre a data de recepção e a data atual.'));
                    }
                },
            ],
            'modo_exclusao_id' => $isRecadastramento
                ? 'nullable|exists:membresia_situacoes,id|required_if:status,I'
                : 'nullable|exists:membresia_situacoes,id',
            'estado_civil' => 'required',
            'nacionalidade' => 'nullable',
            'naturalidade' => 'nullable',
            'profissao' => $isRecadastramento ? 'required|exists:membresia_profissoes,id' : 'nullable|string|max:100',
            'status' => $isRecadastramento
                ? [
                    'required',
                    'in:A,I',
                    function ($attribute, $value, $fail) use ($membroId) {
                        if ($value !== 'A') {
                            return;
                        }

                        $dtExclusaoInformada = !empty($this->input('dt_exclusao'));
                        $modoExclusaoInformado = !empty($this->input('modo_exclusao_id'));
                        if ($dtExclusaoInformada || $modoExclusaoInformado) {
                            $fail(__('Com status Ativo, é necessário limpar Data de Exclusão e Modo de Exclusão. Se precisar manter essas informações, altere o status para Inativo.'));
                        }
                    },
                ]
                : 'nullable|in:A,I',
            'uf' => 'nullable',
            'rol_atual' => $isRecadastramento
                ? [
                    'nullable',
                    'integer',
                    'min:1',
                    new UniqueRolIgrejaRule($membroIdRegraRol, false),
                ]
                : [
                    'required',
                    'integer',
                    'min:1',
                    new UniqueRolIgrejaRule($membroIdRegraRol, false),
                ],
            'cpf' => [
                $isRecadastramento ? 'required_if:status,A' : 'required',
                new ValidaCPF,
                function ($attribute, $value, $fail) use ($membroId, $isRecadastramento, $igrejaRecadastramentoId) {
                    if (empty($value)) {
                        return;
                    }

                    // Remove todos os caracteres que não são números
                    $cpf = preg_replace('/[^0-9]/', '', $value);
                    $consultaCpf = app(ConsultaCpfMembroService::class);
                    $membroDuplicado = $isRecadastramento
                        ? $consultaCpf->findMembroDuplicadoRecadastramento($cpf, $membroId, $igrejaRecadastramentoId)
                        : $consultaCpf->findMembroDuplicado($cpf, $membroId);

                    if (!$membroDuplicado) {
                        return;
                    }

                    if (!$isRecadastramento) {
                        $fail($consultaCpf->mensagemPertence($membroDuplicado));
                        return;
                    }

                    $statusInclusao = $this->input('status');

                    if ($statusInclusao === 'I') {
                        $fail($consultaCpf->mensagemInclusaoInativoBloqueada($membroDuplicado));
                        return;
                    }

                    if ($consultaCpf->isMesmaIgreja($membroDuplicado, $igrejaRecadastramentoId)) {
                        $fail($consultaCpf->mensagemPropriaIgreja($membroDuplicado));
                        return;
                    }

                    if ($consultaCpf->isAtivo($membroDuplicado)) {
                        $fail($consultaCpf->mensagemAtivoOutraIgreja($membroDuplicado));
                        return;
                    }

                    // Membro inativo em outra igreja segue para a confirmação no service.
                },
            ],
            'email_preferencial' => ['nullable', 'email', function ($attribute, $value, $fail) {
                if ($value) {
                    if (!preg_match('/@.*\.\w{2,}$/', $value)) {
                        $fail(__('O campo e-mail deve conter um sufixo de domínio válido com pelo menos dois caracteres após o ponto.'));
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
                function ($attribute, $value, $fail) use ($dataNascimento) {
                    if (empty($value)) {
                        return;
                    }

                    if (strtotime($value) <= strtotime($dataNascimento)) {
                        $fail(__('A data de casamento deve ser após a data de nascimento.'));
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
            'dt_recepcao.required' => 'A data de recepção é obrigatória.',
            'modo_recepcao_id.required' => 'O modo de recepção é obrigatório.',
            'dt_exclusao.required_if' => 'Para status Inativo, a data de exclusão é obrigatória.',
            'modo_exclusao_id.required_if' => 'Para status Inativo, o modo de exclusão é obrigatório.',
            'cpf.required_if' => 'O CPF é obrigatório quando o status estiver Ativo.',
            'telefone_preferencial.required' => 'O campo Telefone é obrigatório.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'endereco.required' => 'O campo Endereço é obrigatório.',
            'numero.required' => 'O campo Número é obrigatório.',
            'bairro.required' => 'O campo Bairro é obrigatório.',
            'cidade.required' => 'O campo Cidade é obrigatório.',
            'estado.required' => 'O campo Estado é obrigatório.',
            'profissao.required' => 'O campo Profissão é obrigatório.',
        ];
    }

    private function igrejaDestinoRecadastramentoId($membroId): ?int
    {
        if (empty($membroId)) {
            return null;
        }

        $igrejaId = DB::table('membresia_migracao')
            ->where('id', $membroId)
            ->value('igreja_id');

        return $igrejaId ? (int) $igrejaId : null;
    }
}
