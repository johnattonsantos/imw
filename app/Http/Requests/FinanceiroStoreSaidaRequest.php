<?php

namespace App\Http\Requests;

use App\Rules\TodaysDeadlineRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class FinanceiroStoreSaidaRequest extends FormRequest
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
            'plano_conta_id' => 'required',
            'caixa_id'       => 'required',
            'valor'          => 'required',
            'data_movimento' =>  ['required', new TodaysDeadlineRule],
            'descricao'      => 'nullable|string',
            'tipo_pagante_favorecido_id' => 'required',
            'anexo1' => 'nullable|file|max:10240|mimes:jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx',
            'descricao_anexo1' => 'nullable|string|max:255',
            'anexo2' => 'nullable|file|max:10240|mimes:jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx',
            'descricao_anexo2' => 'nullable|string|max:255',
            'anexo3' => 'nullable|file|max:10240|mimes:jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx',
            'descricao_anexo3' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $dataMovimento = $this->input('data_movimento');
            $data = Carbon::parse($dataMovimento);
            $mes = $data->month;
            $ano = $data->year;

            $consolidado = DB::table('financeiro_saldo_consolidado_mensal')
                ->where('mes', $mes)
                ->where('ano', $ano)
                ->where('instituicao_id', session()->get('session_perfil')->instituicao_id)
                ->count();

            if ($consolidado > 0) {
                $validator->errors()->add('data_movimento', 'Este mês já foi consolidado.');
            }
        });
    }
}
