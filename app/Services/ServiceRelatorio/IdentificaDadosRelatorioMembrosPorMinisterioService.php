<?php 

namespace App\Services\ServiceRelatorio;

use App\Models\MembresiaFuncaoMinisterial;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use DB;

class IdentificaDadosRelatorioMembrosPorMinisterioService
{
    use Identifiable;

    public function execute(array $request = [])
    {
        
       $ministerios = [
                'todos' => 'Todos os Ministérios',
                'kids' => 'Kids (até 11 anos)',
                'conexao' => 'Conexão (até 23 anos)',
                'fire' => 'Fire (até 17 anos)',
                'move' => 'Move (até 30 anos)',
                'mulheres' => 'Mulheres (todas as idades)',
                'homens' => 'Homens (todas as idades)',
                '60+' => '60+ (acima de 60 anos)',
            ];
            $ministerioSelecionado = (string) isset($request['ministerio'])?$request['ministerio']:'todos' ;
            if (!array_key_exists($ministerioSelecionado, $ministerios)) {
                $ministerioSelecionado = 'todos';
            }

            $idadeExpr = "TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE())";
            $igrejaId = (int) (data_get(session('session_perfil'), 'instituicoes.igrejaLocal.id')
                ?? data_get(session('session_perfil'), 'instituicao_id'));

            $query = DB::table('membresia_membros as mm')
                ->leftJoin('membresia_contatos as mc', 'mc.membro_id', '=', 'mm.id')
                ->select(
                    'mm.nome',
                    DB::raw("COALESCE(NULLIF(mc.telefone_preferencial, ''), NULLIF(mc.telefone_whatsapp, ''), '-') as contato")
                )
                ->where('mm.igreja_id', $igrejaId)
                ->where('mm.vinculo', MembresiaMembro::VINCULO_MEMBRO)
                ->where('mm.status', 'A');

            switch ($ministerioSelecionado) {
                case 'todos':
                    break;
                case 'kids':
                    $query->whereNotNull('mm.data_nascimento')
                        ->whereRaw("$idadeExpr <= 11");
                    break;
                case 'conexao':
                    $query->whereNotNull('mm.data_nascimento')
                        ->whereRaw("$idadeExpr <= 23");
                    break;
                case 'fire':
                    $query->whereNotNull('mm.data_nascimento')
                        ->whereRaw("$idadeExpr <= 17");
                    break;
                case 'move':
                    $query->whereNotNull('mm.data_nascimento')
                        ->whereRaw("$idadeExpr <= 30");
                    break;
                case 'mulheres':
                    $query->where('mm.sexo', 'F');
                    break;
                case 'homens':
                    $query->where('mm.sexo', 'M');
                    break;
                case '60+':
                    $query->whereNotNull('mm.data_nascimento')
                        ->whereRaw("$idadeExpr > 60");
                    break;
            }

            $integrantes = $query->orderBy('mm.nome')->get();  
            $ministerioNome = $ministerios[$ministerioSelecionado];
            $quantidadeIntegrantes = $integrantes->count();
        return ['integrantes'=>$integrantes,'ministerios'=>$ministerios,'ministerioSelecionado'=>$ministerioSelecionado,'ministerioNome'=>$ministerioNome,'quantidadeIntegrantes'=>$quantidadeIntegrantes];
    }

}