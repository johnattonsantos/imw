<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoaNomeacao;
use App\Traits\RegionalScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FinalizarNomeacoesClerigos
{
    use RegionalScope;

    public function execute($id, $request)
    {
        try {
            $regiaoId = $this->sessionRegiaoId();
            $nomeacao = PessoaNomeacao::where('id', $id)
                ->whereHas('pessoa', function ($query) use ($regiaoId) {
                    $query->where('regiao_id', $regiaoId);
                })
                ->firstOrFail();
            $nomeacao->data_termino = $request->input('data_termino');
            $nomeacao->save();
            return redirect()->route('clerigos.nomeacoes.index', ['id' => $id])->with('success', 'Nomeação finalizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Ocorreu um erro.'])->withInput();
        }
    }
}
