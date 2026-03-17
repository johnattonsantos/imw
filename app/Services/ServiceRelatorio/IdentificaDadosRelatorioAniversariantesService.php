<?php 

namespace App\Services\ServiceRelatorio;

use App\Models\CongregacoesCongregacao;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IdentificaDadosRelatorioAniversariantesService
{
    use Identifiable;

    public function execute(array $params = [])
    {
        $data = [
            'congregacoes' => Identifiable::fetchCongregacoes(),
            'render'       => isset($params['action']) && $params['action'] == 'relatorio' ? 'pdf' : 'view'
        ];

        if(isset($params['action'])) {
            $data['membros']      = $this->fetchMembrosRelatorio($params);
            $data['vinculos']     = $this->fetchTextVinculos($params['vinculo']);
            $data['mes']          = $this->getTextMonth($params['mes']);
            $data['ondeCongrega'] = $this->fetchTextCongregacao($params['congregacao_id']);
        }

        return $data;
    }

    private function fetchMembrosRelatorio($params)
    {
        $data = MembresiaMembro::with('congregacao')
            ->select(
                'membresia_membros.id',
                'membresia_membros.congregacao_id',
                'nome', 
                'data_nascimento',
                DB::raw("DATE_FORMAT(data_nascimento, '%d/%m') aniversario"),
                DB::raw("TIMESTAMPDIFF(YEAR, data_nascimento, curdate()) idade"),
                DB::raw("CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE telefone_whatsapp END contato")
            )
            ->join('membresia_contatos', 'membresia_contatos.membro_id', 'membresia_membros.id')
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->where('status', 'A')
            ->when($params['vinculo'], fn($query) => $query->whereIn('vinculo', $params['vinculo']))
            ->when($params['congregacao_id'], fn ($query) => $query->where('congregacao_id', $params['congregacao_id']))
            ->when($params['mes'], fn ($query) => $query->whereMonth('data_nascimento', $params['mes']))->orderBy('nome')
            ->get();
           
        return $data;
    }

    private function fetchTextVinculos($vinculos)
    {
        if (!$vinculos) 
            return 'MEMBROS, CONGREGADOS, VISITANTES';

        $result = [];

        if(in_array('M', $vinculos)) {
            $result[] = 'MEMBROS';
        }

        if(in_array('C', $vinculos)) {
            $result[] = 'CONGREGADOS';
        }

        if(in_array('V', $vinculos)) {
            $result[] = 'VISITANTES';
        }

        return implode(', ', $result);
    }


    private function fetchTextCongregacao($congregacaoId)
    {
        if (!$congregacaoId) 
            return 'TODOS';

        return CongregacoesCongregacao::find($congregacaoId)->nome;
    }

    private function getTextMonth($index) {
        if(!$index) return 'TODOS';

        $meses = [
            1  => "JANEIRO",
            2  => "FEVEREIRO",
            3  => "MARÇO",
            4  => "ABRIL",
            5  => "MAIO",
            6  => "JUNHO",
            7  => "JULHO",
            8  => "AGOSTO",
            9  => "SETEMBRO",
            10 => "OUTUBRO",
            11 => "NOVEMBRO",
            12 => "DEZEMBRO"
        ];
    
        return $meses[$index];
    }
}