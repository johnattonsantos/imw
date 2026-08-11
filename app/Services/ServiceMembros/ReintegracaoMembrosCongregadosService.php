<?php

namespace App\Services\ServiceMembros;

use App\Models\GCeuMembros;
use App\Models\MembresiaFuncaoMinisterial;
use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Models\MembresiaSituacao;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReintegracaoMembrosCongregadosService
{
    use Identifiable;

    public function dadosTela(?string $cpf = null): array
    {
        $cpf = $this->normalizeCpf($cpf);
        $resultado = null;

        if ($cpf !== '') {
            $resultado = $this->consultarCpf($cpf);
        }

        return [
            'cpf' => $cpf,
            'resultado' => $resultado,
            'modos' => Identifiable::fetchModos(MembresiaSituacao::TIPO_ADESAO),
            'pastores' => Identifiable::fetchPastores(),
            'congregacoes' => Identifiable::fetchCongregacoes(),
        ];
    }

    public function consultarCpf(string $cpf): array
    {
        $cpf = $this->normalizeCpf($cpf);
        $pessoas = MembresiaMembro::withTrashed()
            ->where('cpf', $cpf)
            ->orderByRaw("CASE WHEN status = 'A' AND deleted_at IS NULL THEN 0 ELSE 1 END")
            ->orderByDesc('updated_at')
            ->get();

        if ($pessoas->isEmpty()) {
            return [
                'status' => 'nao_cadastrada',
                'message' => __('Pessoa não cadastrada no sistema. Para cadastrar esta pessoa, utilize a função Congregados → Incluir congregado.'),
            ];
        }

        $igrejaAtualId = Identifiable::fetchSessionIgrejaLocal()->id;
        $consultaCpf = app(ConsultaCpfMembroService::class);

        $membroAtivoOutraIgreja = $pessoas->first(function ($pessoa) use ($igrejaAtualId, $consultaCpf) {
            return $pessoa->vinculo === MembresiaMembro::VINCULO_MEMBRO
                && $consultaCpf->isAtivo($pessoa)
                && (int) $pessoa->igreja_id !== (int) $igrejaAtualId;
        });

        if ($membroAtivoOutraIgreja) {
            return [
                'status' => 'bloqueado',
                'pessoa' => $membroAtivoOutraIgreja,
                'message' => $this->mensagemMembroAtivoOutraIgreja($membroAtivoOutraIgreja),
                'origem' => $consultaCpf->origemDetalhada($membroAtivoOutraIgreja),
            ];
        }

        $pessoaAtiva = $pessoas->first(fn ($pessoa) => $consultaCpf->isAtivo($pessoa));
        if ($pessoaAtiva) {
            return [
                'status' => 'bloqueado',
                'pessoa' => $pessoaAtiva,
                'message' => __('Esta pessoa já está ativa no sistema e não pode ser reintegrada por esta tela.'),
                'origem' => $consultaCpf->origemDetalhada($pessoaAtiva),
            ];
        }

        $pessoa = $pessoas->first();
        $ultimaExclusao = $this->ultimaDataExclusao($pessoa->id);

        return [
            'status' => 'apta',
            'pessoa' => $pessoa,
            'origem' => $consultaCpf->origemDetalhada($pessoa),
            'ultima_exclusao' => $ultimaExclusao,
            'sugestao_rol' => Identifiable::fetchSugestaoRol(),
        ];
    }

    public function reintegrar(array $data): string
    {
        $pessoa = MembresiaMembro::withTrashed()->findOrFail($data['membro_id']);
        $consulta = $this->consultarCpf($pessoa->cpf);

        if (($consulta['status'] ?? null) !== 'apta' || (string) $consulta['pessoa']->id !== (string) $pessoa->id) {
            throw new RuntimeException($consulta['message'] ?? __('Não foi possível reintegrar esta pessoa.'));
        }

        if ($data['destino'] === MembresiaMembro::VINCULO_CONGREGADO) {
            $this->reintegrarComoCongregado($pessoa);
            return MembresiaMembro::VINCULO_CONGREGADO;
        }

        app(StoreReintegracaoService::class)->execute([
            'numero_rol' => $data['numero_rol'],
            'dt_recepcao' => $data['dt_recepcao'],
            'modo_recepcao_id' => $data['modo_recepcao_id'],
            'clerigo_id' => $data['clerigo_id'] ?? null,
            'congregacao_id' => $data['congregacao_id'] ?? null,
        ], $pessoa->id);

        return MembresiaMembro::VINCULO_MEMBRO;
    }

    private function reintegrarComoCongregado(MembresiaMembro $pessoa): void
    {
        DB::transaction(function () use ($pessoa) {
            $instituicoes = Identifiable::fetchSessionInstituicoesStoreMembresia();
            $reintegracaoOutraIgreja = (int) $pessoa->igreja_id !== (int) $instituicoes['igreja_id'];

            $pessoa->restore();
            $dados = [
                'status' => MembresiaMembro::STATUS_ATIVO,
                'vinculo' => MembresiaMembro::VINCULO_CONGREGADO,
                'rol_atual' => null,
                'congregacao_id' => null,
                'regiao_id' => $instituicoes['regiao_id'],
                'distrito_id' => $instituicoes['distrito_id'],
                'igreja_id' => $instituicoes['igreja_id'],
            ];

            if ($reintegracaoOutraIgreja) {
                $dados['funcao_eclesiastica_id'] = null;
                $dados['gceu_id'] = null;
            }

            $pessoa->update($dados);

            if ($reintegracaoOutraIgreja) {
                MembresiaFuncaoMinisterial::where('membro_id', $pessoa->id)->delete();
                GCeuMembros::where('membro_id', $pessoa->id)->delete();
            }
        });
    }

    private function mensagemMembroAtivoOutraIgreja(MembresiaMembro $pessoa): string
    {
        $origem = app(ConsultaCpfMembroService::class)->origemDetalhada($pessoa);

        return __('":nome" é membro ativo na igreja :igreja, Distrito :distrito, :regiao. Para proceder com a inclusão, a igreja de origem deve iniciar o procedimento de transferência, para ser aceito na igreja atual.', [
            'nome' => $pessoa->nome,
            'igreja' => $origem['igreja'] ?: '-',
            'distrito' => $origem['distrito'] ?: '-',
            'regiao' => $origem['regiao'] ?: '-',
        ]);
    }

    private function ultimaDataExclusao(string $membroId): ?Carbon
    {
        $rol = MembresiaRolPermanente::withTrashed()
            ->where('membro_id', $membroId)
            ->where(function ($query) {
                $query->where('status', MembresiaRolPermanente::STATUS_EXCLUSAO)
                    ->orWhereNotNull('dt_exclusao');
            })
            ->orderByDesc('dt_exclusao')
            ->orderByDesc('id')
            ->first();

        return $rol && $rol->dt_exclusao ? Carbon::parse($rol->dt_exclusao) : null;
    }

    private function normalizeCpf(?string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', (string) $cpf);
    }
}
