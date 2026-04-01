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
                'kids' => 'Kid (até 9 anos)',
                'conexao' => 'Conexão (até 13 anos)',
                'fire' => 'Fire (até 17 anos)',
                'move' => 'Move (até 30 anos)',
                'mulheres' => 'Mulheres (todas as idades)',
                'homens' => 'Homens (todas as idades)',
                '60+' => '60+ (acima de 60 anos)',
            ];
            $ministerioSelecionado = (string) (isset($request['ministerio']) ? $request['ministerio'] : 'todos');
            if (!array_key_exists($ministerioSelecionado, $ministerios)) {
                $ministerioSelecionado = 'todos';
            }
            $nomeacaoAtiva = !empty($request['nomeacao_ativa']);
            $vinculosSelecionados = collect($request['vinculo'] ?? [
                'nao_congregado',
                'congregado',
            ])->filter()->values()->all();

            $mapVinculos = [
                'nao_congregado' => MembresiaMembro::VINCULO_MEMBRO,
                'congregado' => MembresiaMembro::VINCULO_CONGREGADO,
            ];

            $vinculosDb = collect($vinculosSelecionados)
                ->map(fn($item) => $mapVinculos[$item] ?? null)
                ->filter()
                ->unique()
                ->values()
                ->all();

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
                ->where('mm.status', 'A');

            if (!empty($vinculosDb)) {
                $query->whereIn('mm.vinculo', $vinculosDb);
            } else {
                // Nenhum vínculo marcado: resultado vazio.
                $query->whereRaw('1 = 0');
            }

            if ($nomeacaoAtiva) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('membresia_funcoesministeriais as mfm')
                        ->whereColumn('mfm.membro_id', 'mm.id')
                        ->whereNull('mfm.data_saida')
                        ->whereNull('mfm.deleted_at');
                });
            }

            switch ($ministerioSelecionado) {
                case 'todos':
                    break;
                case 'kids':
                    $query->whereNotNull('mm.data_nascimento')
                        ->whereRaw("$idadeExpr <= 9");
                    break;
                case 'conexao':
                    $query->whereNotNull('mm.data_nascimento')
                        ->whereRaw("$idadeExpr <= 13");
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

            return [
                'integrantes' => $integrantes,
                'ministerios' => $ministerios,
                'ministerioSelecionado' => $ministerioSelecionado,
                'ministerioNome' => $ministerioNome,
                'quantidadeIntegrantes' => $quantidadeIntegrantes,
                'nomeacaoAtiva' => $nomeacaoAtiva,
                'vinculosSelecionados' => $vinculosSelecionados,
            ];
    }

}
