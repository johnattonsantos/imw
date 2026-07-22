<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoaNomeacao;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FinalizarNomeacoesClerigos
{
    public function execute($id, $request)
    {
        try {
            $nomeacao = PessoaNomeacao::findOrFail($id);
            $nomeacao->data_termino = $request->input('data_termino');
            $nomeacao->save();
            return redirect()->route('clerigos.nomeacoes.index', ['id' => $id])->with('success', __('Nomeação finalizada com sucesso!'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Ocorreu um erro.'])->withInput();
        }
    }
}
