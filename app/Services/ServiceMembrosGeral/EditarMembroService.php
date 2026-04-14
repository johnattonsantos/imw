<?php

namespace App\Services\ServiceMembrosGeral;

use App\Exceptions\MembroNotFoundException;
use App\Models\GCeu;
use App\Models\GCeuFuncoes;
use App\Models\GCeuMembros;
use App\Models\MembresiaCurso;
use App\Models\MembresiaFormacao;
use App\Models\MembresiaFuncaoEclesiastica;
use App\Models\MembresiaMembro;
use App\Models\MembresiaSituacao;
use App\Models\MembresiaSetor;
use App\Models\MembresiaTipoAtuacao;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;


class EditarMembroService
{
    use Identifiable;

    public function findOne($id)
    {
        $pessoa = MembresiaMembro::with(['contato', 'funcoesMinisteriais', 'familiar', 'formacoesEclesiasticas', 'notificacaoTransferenciaAtiva'])
            ->where('id', $id)
            ->firstOr(function () {
                throw new MembroNotFoundException('Registro não encontrado', 404);
            });
        if ($pessoa->foto) {
            $pessoa->foto = $this->resolveFotoUrl((string) $pessoa->foto);
        }
        
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $ministerios = MembresiaSetor::orderBy('descricao', 'asc')->get();
        $funcoes = MembresiaTipoAtuacao::orderBy('descricao', 'asc')->get();
        $cursos = MembresiaCurso::orderBy('nome', 'asc')->get();
        $formacoes = MembresiaFormacao::orderBy('id', 'asc')->get();
        $profissoes = $this->fetchProfissoesAtivas();
        $funcoesEclesiasticas = MembresiaFuncaoEclesiastica::orderBy('descricao', 'asc')->get();
        $gceus = GCeu::where(['status' => 'A', 'instituicao_id' => $igrejaId])->orderBy('nome', 'asc')->get();
        $gceuFuncoes = GCeuFuncoes::orderBy('funcao', 'asc')->get();
        $gceuMembros = GCeuMembros::where(['membro_id' => $id])->get();
        return [
            'pessoa'               => $pessoa,
            'ministerios'          => $ministerios,
            'funcoes'              => $funcoes,
            'cursos'               => $cursos,
            'formacoes'            => $formacoes,
            'profissoes'           => $profissoes,
            'funcoesEclesiasticas' => $funcoesEclesiasticas,
            'congregacoes'         => Identifiable::fetchCongregacoes(),
            'modosRecepcao'        => Identifiable::fetchModos(MembresiaSituacao::TIPO_ADESAO),
            'modosExclusao'        => Identifiable::fetchModos(MembresiaSituacao::TIPO_EXCLUSAO),
            'gceus'                => $gceus,
            'gceuFuncoes'          => $gceuFuncoes,
            'gceuMembros'          => $gceuMembros
        ];
    }

    private function fetchProfissoesAtivas()
    {
        if (!Schema::hasTable('membresia_profissoes')) {
            return collect();
        }

        $query = DB::table('membresia_profissoes');

        if (Schema::hasColumn('membresia_profissoes', 'ativo')) {
            $query->where('ativo', 1);
        } elseif (Schema::hasColumn('membresia_profissoes', 'status')) {
            $query->where(function ($q) {
                $q->where('status', 1)->orWhere('status', 'A');
            });
        }

        return $query->get()->sortBy(function ($profissao) {
            return mb_strtolower(trim(
                $profissao->descricao
                ?? $profissao->nome
                ?? $profissao->profissao
                ?? (string) $profissao->id
            ));
        })->values();
    }

    private function resolveFotoUrl(string $foto): string
    {
        // Já é URL completa salva no banco: usa como está.
        if (Str::startsWith($foto, ['http://', 'https://'])) {
            return $foto;
        }

        try {
            return Storage::disk('s3')->temporaryUrl($foto, Carbon::now()->addMinutes(15));
        } catch (\Throwable $e) {
            try {
                return Storage::disk('s3')->url($foto);
            } catch (\Throwable $e) {
                return $foto;
            }
        }
    }
}
