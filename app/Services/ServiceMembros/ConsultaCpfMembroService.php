<?php

namespace App\Services\ServiceMembros;

use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class ConsultaCpfMembroService
{
    use Identifiable;

    public function findMembroDuplicado(?string $cpf, ?string $ignoreMembroId = null): ?MembresiaMembro
    {
        $cpf = $this->normalizeCpf($cpf);
        if ($cpf === '') {
            return null;
        }

        return MembresiaMembro::withTrashed()
            ->where('cpf', $cpf)
            ->where('vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->when($ignoreMembroId, fn ($query) => $query->where('id', '!=', $ignoreMembroId))
            ->orderByRaw("CASE WHEN status = ? AND deleted_at IS NULL THEN 0 ELSE 1 END", [MembresiaMembro::STATUS_ATIVO])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();
    }

    public function findMembroDuplicadoRecadastramento(?string $cpf, ?string $membroMigracaoId, ?int $igrejaId = null): ?MembresiaMembro
    {
        $cpf = $this->normalizeCpf($cpf);
        if ($cpf === '') {
            return null;
        }

        $igrejaId = $igrejaId ?: self::fetchSessionIgrejaLocal()->id;

        return MembresiaMembro::withTrashed()
            ->where('cpf', $cpf)
            ->where('vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->orderByRaw("CASE WHEN status = ? AND deleted_at IS NULL THEN 0 ELSE 1 END", [MembresiaMembro::STATUS_ATIVO])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get()
            ->first(function (MembresiaMembro $membro) use ($membroMigracaoId, $igrejaId) {
                $mesmoRegistroDaIgrejaValidada = (string) $membro->id === (string) $membroMigracaoId
                    && (int) $membro->igreja_id === (int) $igrejaId;

                return !$mesmoRegistroDaIgrejaValidada;
            });
    }

    public function findMembroAtivo(?string $cpf, ?string $ignoreMembroId = null): ?MembresiaMembro
    {
        $cpf = $this->normalizeCpf($cpf);
        if ($cpf === '') {
            return null;
        }

        return MembresiaMembro::withTrashed()
            ->where('cpf', $cpf)
            ->where('vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->where('status', MembresiaMembro::STATUS_ATIVO)
            ->whereNull('deleted_at')
            ->when($ignoreMembroId, fn ($query) => $query->where('id', '!=', $ignoreMembroId))
            ->first();
    }

    public function findMembroInativo(?string $cpf, ?string $ignoreMembroId = null): ?MembresiaMembro
    {
        $cpf = $this->normalizeCpf($cpf);
        if ($cpf === '') {
            return null;
        }

        return MembresiaMembro::withTrashed()
            ->where('cpf', $cpf)
            ->where('vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->where(function ($query) {
                $query->where('status', '<>', MembresiaMembro::STATUS_ATIVO)
                    ->orWhereNotNull('deleted_at');
            })
            ->when($ignoreMembroId, fn ($query) => $query->where('id', '!=', $ignoreMembroId))
            ->first();
    }

    public function isAtivo(MembresiaMembro $membro): bool
    {
        return $membro->status === MembresiaMembro::STATUS_ATIVO && $membro->deleted_at === null;
    }

    public function isMesmaIgreja(MembresiaMembro $membro, ?int $igrejaId = null): bool
    {
        $igrejaId = $igrejaId ?: self::fetchSessionIgrejaLocal()->id;

        return (int) $membro->igreja_id === (int) $igrejaId;
    }

    public function origemFormatada(MembresiaMembro $membro): string
    {
        $partes = array_filter($this->origemDetalhada($membro));

        return $partes ? implode(' / ', $partes) : 'igreja de origem não identificada';
    }

    public function origemDetalhada(MembresiaMembro $membro): array
    {
        $regiao = $membro->regiao_id;
        $distrito = $membro->distrito_id;
        $igreja = $membro->igreja_id;

        $nomes = DB::table('instituicoes_instituicoes')
            ->whereIn('id', array_filter([$regiao, $distrito, $igreja]))
            ->pluck('nome', 'id');

        return [
            'regiao' => $regiao ? $nomes->get($regiao) : null,
            'distrito' => $distrito ? $nomes->get($distrito) : null,
            'igreja' => $igreja ? $nomes->get($igreja) : null,
        ];
    }

    public function dataDesligamentoFormatada(MembresiaMembro $membro): string
    {
        $rol = $this->ultimoRolInativo($membro) ?: $this->ultimoRol($membro);
        $data = optional($rol)->dt_exclusao;

        return $data ? $data->format('d/m/Y') : 'data não informada';
    }

    public function mensagemAtivo(MembresiaMembro $membro): string
    {
        return 'Este CPF pertence a um membro ativo em ' . $this->origemFormatada($membro) . '. A igreja de origem deve ser contactada para proceder com a transferência.';
    }

    public function mensagemPertence(MembresiaMembro $membro): string
    {
        $origem = $this->origemDetalhada($membro);

        return __('Esse CPF pertence a :nome, :regiao, :distrito, :igreja', [
            'nome' => $membro->nome ?: '-',
            'regiao' => $origem['regiao'] ?: '-',
            'distrito' => $origem['distrito'] ?: '-',
            'igreja' => $origem['igreja'] ?: '-',
        ]);
    }

    public function mensagemInativo(MembresiaMembro $membro): string
    {
        return 'Este CPF pertence a um membro desligado de ' . $this->origemFormatada($membro) . ', em ' . $this->dataDesligamentoFormatada($membro) . '. Se desejar, confirme a reintegração nesta igreja. Apenas os dados pessoais serão preservados; funções, ministérios e vínculos eclesiásticos locais não serão transferidos.';
    }

    public function mensagemConfirmacaoInativoOutraIgreja(MembresiaMembro $membro): string
    {
        $origem = $this->origemDetalhada($membro);

        return __('Este CPF pertence a :nome, que está registrado como membro INATIVO na igreja :igreja, distrito :distrito, :regiao. Deseja continuar com a inclusão?', [
            'nome' => $membro->nome ?: '-',
            'igreja' => $origem['igreja'] ?: '-',
            'distrito' => $origem['distrito'] ?: '-',
            'regiao' => $origem['regiao'] ?: '-',
        ]);
    }

    public function mensagemPropriaIgreja(MembresiaMembro $membro): string
    {
        return __('Este CPF pertence a :nome, que já está registrado nesta igreja. Caso necessário, utilize a função de "Reintegração" para trazer o membro de volta ao rol ativo.', [
            'nome' => $membro->nome ?: '-',
        ]);
    }

    public function mensagemAtivoOutraIgreja(MembresiaMembro $membro): string
    {
        $origem = $this->origemDetalhada($membro);

        return __('Este CPF pertence a :nome, que está registrado como membro ATIVO na igreja :igreja, distrito :distrito, :regiao. Caso este tenha se transferido para esta igreja, entre em contato com a igreja de origem para que seja iniciado o processo de transferência, ou entre em contato com o suporte do IMWPlus para obter mais informações.', [
            'nome' => $membro->nome ?: '-',
            'igreja' => $origem['igreja'] ?: '-',
            'distrito' => $origem['distrito'] ?: '-',
            'regiao' => $origem['regiao'] ?: '-',
        ]);
    }

    public function mensagemInclusaoInativoBloqueada(MembresiaMembro $membro): string
    {
        $origem = $this->origemDetalhada($membro);

        return __('Este CPF pertence a :nome, que está registrado como membro na igreja :igreja, portanto não há como adicioná-lo no sistema.', [
            'nome' => $membro->nome ?: '-',
            'igreja' => $origem['igreja'] ?: '-',
        ]);
    }

    private function ultimoRol(MembresiaMembro $membro): ?MembresiaRolPermanente
    {
        return MembresiaRolPermanente::withTrashed()
            ->where('membro_id', $membro->id)
            ->orderByDesc('lastrec')
            ->orderByDesc('dt_recepcao')
            ->orderByDesc('id')
            ->first();
    }

    private function ultimoRolInativo(MembresiaMembro $membro): ?MembresiaRolPermanente
    {
        return MembresiaRolPermanente::withTrashed()
            ->where('membro_id', $membro->id)
            ->where(function ($query) {
                $query->where('status', MembresiaRolPermanente::STATUS_EXCLUSAO)
                    ->orWhereNotNull('dt_exclusao');
            })
            ->orderByDesc('dt_exclusao')
            ->orderByDesc('id')
            ->first();
    }

    private function normalizeCpf(?string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', (string) $cpf);
    }
}
