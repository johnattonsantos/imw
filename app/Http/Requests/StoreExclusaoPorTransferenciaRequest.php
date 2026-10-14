<?php

namespace App\Http\Requests;

use App\Rules\DatePreviousToRecebimentoRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExclusaoPorTransferenciaRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge(['dt_notificacao' => now()->toDateString()]);
    }

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
            'dt_notificacao' => ['required', new DatePreviousToRecebimentoRule($membroId)],
            'igreja_id'      => 'required'
        ];
    }
}
