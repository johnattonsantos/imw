<?php

namespace App\Http\Controllers;

use App\Models\MembresiaMembro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidarMembroController extends Controller
{
    public function show(Request $request)
    {
        $membroId = trim((string) $request->query('membro'));
        $membro = $membroId !== '' ? $this->findMembro($membroId) : null;

        if ($membro) {
            $membro->status_descricao = $this->statusDescricao($membro->status);
        }

        return view('membros.carteirinhas.validar', [
            'membro' => $membro,
            'membroId' => $membroId,
        ]);
    }

    private function findMembro(string $membroId): ?object
    {
        return DB::table('membresia_membros as mm')
            ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'mm.igreja_id')
            ->leftJoin('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pn.instituicao_id', '=', 'igreja.id')
                    ->whereNull('pn.data_termino')
                    ->whereNull('pn.deleted_at');
            })
            ->leftJoin('pessoas_pessoas as pastor', function ($join) {
                $join->on('pastor.id', '=', 'pn.pessoa_id')
                    ->where('pastor.categoria', 'pastor')
                    ->whereNull('pastor.deleted_at');
            })
            ->leftJoin('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->where('mm.id', $membroId)
            ->whereNull('mm.deleted_at')
            ->select([
                'mm.id',
                'mm.nome',
                'mm.status',
                'igreja.nome as igreja_nome',
                DB::raw("COALESCE(MAX(CASE WHEN pf.ordem IN (3, 4, 5) THEN pastor.nome END), MAX(pastor.nome), igreja.pastor) as pastor_nome"),
            ])
            ->groupBy('mm.id', 'mm.nome', 'mm.status', 'igreja.nome', 'igreja.pastor')
            ->first();
    }

    private function statusDescricao(?string $status): string
    {
        return match ($status) {
            MembresiaMembro::STATUS_ATIVO => __('Ativo'),
            MembresiaMembro::STATUS_INATIVO => __('Inativo'),
            default => __('Não informado'),
        };
    }
}
