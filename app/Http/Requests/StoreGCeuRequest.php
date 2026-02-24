<?php

namespace App\Http\Requests;

use App\Rules\RangeDateRule;
use App\Rules\VisitanteExistenteRule;
use App\Rules\ValidDateOfBirth;
use Illuminate\Foundation\Http\FormRequest;

class StoreGCeuRequest extends FormRequest
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
            'nome' => 'required',
            'congregacao_id' => 'required',
            'cep' => 'required'
        ];
    }
}
