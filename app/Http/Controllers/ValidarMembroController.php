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

        return response()->view('membros.carteirinhas.validar', [
            'membro' => $membro,
            'membroId' => $membroId,
        ])->header('Cache-Control', 'no-store, private');
    }

    private function findMembro(string $membroId): ?object
    {
        $hoje = today()->toDateString();
        $pastorAtual = DB::table('pessoas_nomeacoes as pn')
            ->join('pessoas_pessoas as pastor', 'pastor.id', '=', 'pn.pessoa_id')
            ->join('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->whereColumn('pn.instituicao_id', 'igreja.id')
            ->where('pf.titular', 1)
            ->where('pastor.status_id', 1)
            ->whereDate('pn.data_nomeacao', '<=', $hoje)
            ->where(function ($query) use ($hoje) {
                $query->whereNull('pn.data_termino')
                    ->orWhereDate('pn.data_termino', '>=', $hoje);
            })
            ->whereNull('pn.deleted_at')
            ->whereNull('pastor.deleted_at')
            ->whereNotNull('pastor.nome')
            ->where('pastor.nome', '<>', '')
            ->orderByDesc('pn.data_nomeacao')
            ->orderByDesc('pn.id')
            ->select('pastor.nome')
            ->limit(1);

        return DB::table('membresia_membros as mm')
            ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'mm.igreja_id')
            ->where('mm.id', $membroId)
            ->whereNull('mm.deleted_at')
            ->select([
                'mm.id',
                'mm.nome',
                'mm.status',
                'igreja.nome as igreja_nome',
            ])
            ->selectSub($pastorAtual, 'pastor_nome')
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
