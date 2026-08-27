<?php

namespace App\Http\Requests;

use App\Rules\UniqueRolIgrejaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

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
        $notificacao = Route::current()->parameter('notificacao');

        return [
            'dt_resposta' => 'required|date',
            'congregacao' => 'nullable|exists:congregacoes_congregacoes,id',
            'action'      => 'required',
            'numero_rol'  => ['required', new UniqueRolIgrejaRule($notificacao->membro_id)],
        ];
    }
}
