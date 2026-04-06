<?php 

namespace App\Services\ServiceRelatorio;

use App\Models\MembresiaFuncaoMinisterial;
use App\Models\MembresiaMembro;
use App\Models\MembresiaTipoAtuacao;
use App\Traits\Identifiable;

class IdentificaDadosRelatorioHistoricoEclesiasticoService
{
    use Identifiable;

    public function execute(array $params = [])
    {
        $funcaoSelecionada = $params['funcao_ministerial_id'] ?? '';
        $data = [
            'select'       => $funcaoSelecionada,
            'funcoes'      => $this->fetchFuncoesMinisteriaisRelatorio(),
            'render'       => isset($params['action']) && $params['action'] == 'relatorio' ? 'pdf' : 'view',
            'funcaoMinisterial' => 'TODAS AS FUNÇÕES MINISTERIAIS',
        ];

        if (isset($params['action'])) {
            $data['historicoEclesiastico'] = $this->fetchHistoricoEclesiastico($params);

            if (!empty($funcaoSelecionada) && $funcaoSelecionada !== 'todos') {
                $funcao = MembresiaTipoAtuacao::find($funcaoSelecionada);
                if ($funcao) {
                    $data['funcaoMinisterial'] = strtoupper($funcao->descricao);
                }
            }
        }

        return $data;
    }

    private function fetchFuncoesMinisteriaisRelatorio()
    {
        return MembresiaTipoAtuacao::orderBy('descricao')->get();
    }

    private function fetchHistoricoEclesiastico($params)
    {
        return MembresiaFuncaoMinisterial::with(['membro', 'ministerio', 'tipoAtuacao'])
            ->whereHas('membro', function ($query) {
                $query->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
                    ->where('vinculo', MembresiaMembro::VINCULO_MEMBRO);
            })
            ->when(
                ($params['funcao_ministerial_id'] ?? 'todos') !== 'todos' && !empty($params['funcao_ministerial_id']),
                fn($query) => $query->where('tipoatuacao_id', $params['funcao_ministerial_id'])
            )
            ->when((bool) ($params['nomeacao_ativa'] ?? 0), fn($query) => $query->whereNull('data_saida'))
            ->orderBy('data_entrada', 'desc')
            ->get();
    }
}
