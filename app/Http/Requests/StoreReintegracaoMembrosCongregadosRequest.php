<?php

namespace App\Http\Requests;

use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Rules\PeriodoEclesiasticoCorrenteRule;
use App\Rules\RangeDateRule;
use App\Rules\UniqueRolIgrejaRule;
use App\Services\ServiceMembros\ConsultaCpfMembroService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreReintegracaoMembrosCongregadosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cpf' => preg_replace('/[^0-9]/', '', (string) $this->input('cpf')),
            'congregacao_id' => $this->input('congregacao_id') ?: null,
            'clerigo_id' => $this->input('clerigo_id') ?: null,
        ]);
    }

    public function rules()
    {
        $membroId = $this->input('membro_id');

        return [
            'membro_id' => ['required', 'exists:membresia_membros,id'],
            'cpf' => ['required', 'digits:11'],
            'destino' => ['required', 'in:' . MembresiaMembro::VINCULO_CONGREGADO . ',' . MembresiaMembro::VINCULO_MEMBRO],
            'numero_rol' => [
                'exclude_unless:destino,' . MembresiaMembro::VINCULO_MEMBRO,
                'required_if:destino,' . MembresiaMembro::VINCULO_MEMBRO,
                'nullable',
                'integer',
                'min:1',
                new UniqueRolIgrejaRule($membroId),
            ],
            'dt_recepcao' => [
                'exclude_unless:destino,' . MembresiaMembro::VINCULO_MEMBRO,
                'required_if:destino,' . MembresiaMembro::VINCULO_MEMBRO,
                'nullable',
                'date',
                new RangeDateRule,
                new PeriodoEclesiasticoCorrenteRule,
                function ($attribute, $value, $fail) use ($membroId) {
                    if ($this->input('destino') !== MembresiaMembro::VINCULO_MEMBRO || empty($value)) {
                        return;
                    }

                    $ultimaExclusao = $this->ultimaDataExclusao($membroId);
                    if (!$ultimaExclusao) {
                        return;
                    }

                    if (Carbon::parse($value)->lt($ultimaExclusao)) {
                        $fail(__('A data de recepção não pode ser anterior à data de exclusão anterior (:data).', [
                            'data' => $ultimaExclusao->format('d/m/Y'),
                        ]));
                    }
                },
            ],
            'modo_recepcao_id' => ['exclude_unless:destino,' . MembresiaMembro::VINCULO_MEMBRO, 'required_if:destino,' . MembresiaMembro::VINCULO_MEMBRO, 'nullable', 'exists:membresia_situacoes,id'],
            'clerigo_id' => ['exclude_unless:destino,' . MembresiaMembro::VINCULO_MEMBRO, 'nullable', 'exists:pessoas_pessoas,id'],
            'congregacao_id' => ['exclude_unless:destino,' . MembresiaMembro::VINCULO_MEMBRO, 'nullable', 'exists:congregacoes_congregacoes,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $consultaCpf = app(ConsultaCpfMembroService::class);
            $pessoa = $consultaCpf->findPessoaDuplicada($this->input('cpf'));

            if (!$pessoa || (string) $pessoa->id !== (string) $this->input('membro_id')) {
                $validator->errors()->add('cpf', __('CPF informado não corresponde à pessoa selecionada para reintegração.'));
                return;
            }

            if ($consultaCpf->isAtivo($pessoa)) {
                $validator->errors()->add('cpf', __('Esta pessoa já está ativa no sistema e não pode ser reintegrada por esta tela.'));
            }
        });
    }

    public function messages()
    {
        return [
            'destino.required' => __('Informe como a pessoa será recebida.'),
            'numero_rol.required_if' => __('O número do rol é obrigatório para reintegração como membro.'),
            'dt_recepcao.required_if' => __('A data de recepção é obrigatória para reintegração como membro.'),
            'modo_recepcao_id.required_if' => __('O modo de recepção é obrigatório para reintegração como membro.'),
        ];
    }

    private function ultimaDataExclusao(?string $membroId): ?Carbon
    {
        if (!$membroId) {
            return null;
        }

        $rol = MembresiaRolPermanente::withTrashed()
            ->where('membro_id', $membroId)
            ->where(function ($query) {
                $query->where('status', MembresiaRolPermanente::STATUS_EXCLUSAO)
                    ->orWhereNotNull('dt_exclusao');
            })
            ->orderByDesc('dt_exclusao')
            ->orderByDesc('id')
            ->first();

        return $rol && $rol->dt_exclusao ? Carbon::parse($rol->dt_exclusao)->startOfDay() : null;
    }
}
