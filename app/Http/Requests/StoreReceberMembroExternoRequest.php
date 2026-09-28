<?php

namespace App\Http\Requests;

use App\Rules\RangeDateRule;
use App\Rules\UniqueRolIgrejaRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreReceberMembroExternoRequest extends FormRequest
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
        $notificacao = $this->route('notificacao');
        $dataAbertura = Carbon::parse($notificacao->dt_abertura)->format('Y-m-d');

        return [
            'dt_resposta' => [
                'bail',
                'required',
                'date',
                new RangeDateRule(),
                'after_or_equal:'.$dataAbertura,
            ],
            'congregacao' => 'nullable|exists:congregacoes_congregacoes,id',
            'action' => 'required',
            'numero_rol' => ['required', new UniqueRolIgrejaRule($notificacao->membro_id)],
        ];
    }

    public function messages()
    {
        return [
            'dt_resposta.required' => 'A data é obrigatória.',
            'dt_resposta.date' => 'Informe uma data válida.',
            'dt_resposta.after_or_equal' => 'A data não pode ser anterior à data da transferência.',
        ];
    }
}
