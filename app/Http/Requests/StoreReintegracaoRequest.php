<?php

namespace App\Http\Requests;

use App\Rules\RangeDateRule;
use App\Rules\UniqueRolIgrejaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class StoreReintegracaoRequest extends FormRequest
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
        $membroId = Route::current()->parameter('id');

        return [
            "numero_rol"       => ['required', new UniqueRolIgrejaRule($membroId)],
            "dt_recepcao"      => ['required', 'date', new RangeDateRule],
            "modo_recepcao_id" => 'required|exists:membresia_situacoes,id',
            "clerigo_id"       => 'nullable|exists:pessoas_pessoas,id',
            "congregacao_id"   => 'nullable|exists:congregacoes_congregacoes,id',
        ];
    }

    public function messages()
    {
        return [
            "dt_recepcao.required"      => 'Este campo é obrigatório',
            "modo_recepcao_id.required" => 'Este campo é obrigatório',
        ];
    }
}
