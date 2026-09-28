<?php

namespace App\Http\Requests;

use App\Rules\DatePreviousToRecebimentoRule;
use App\Rules\RangeDateRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExclusaoPorTransferenciaRequest extends FormRequest
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
     * @return array<string, mixed
     */
    public function rules()
    {
        $membroId = $this->route('id');

        return [
            'dt_notificacao' => [
                'bail',
                'required',
                'date',
                new RangeDateRule(),
                new DatePreviousToRecebimentoRule($membroId),
            ],
            'igreja_id' => 'required',
        ];
    }
}
