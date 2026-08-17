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
            ->first();
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

    public function origemFormatada(MembresiaMembro $membro): string
    {
        $partes = array_filter($this->origemDetalhada($membro));

        return $partes ? implode(' / ', $partes) : 'igreja de origem não identificada';
    }

    public function origemDetalhada(MembresiaMembro $membro): array
    {
        $rol = $this->ultimoRol($membro);

        $regiao = optional($rol)->regiao_id ?: $membro->regiao_id;
        $distrito = optional($rol)->distrito_id ?: $membro->distrito_id;
        $igreja = optional($rol)->igreja_id ?: $membro->igreja_id;

        $nomes = DB::table('instituicoes_instituicoes')
            ->whereIn('id', array_filter([$regiao, $distrito, $igreja]))
            ->pluck('nome', 'id');

        return [
            'regiao' => $regiao ? $nomes->get($regiao) : null,
            'distrito' => $distrito ? $nomes->get($distrito) : null,
            'igreja' => $igreja ? $nomes->get($igreja) : null,
        ];
    }

    public function igrejaOrigemId(MembresiaMembro $membro): ?int
    {
        $rol = $this->ultimoRol($membro);
        $igrejaId = optional($rol)->igreja_id ?: $membro->igreja_id;

        return $igrejaId ? (int) $igrejaId : null;
    }

    public function isOutraIgreja(MembresiaMembro $membro, ?int $igrejaDestinoId): bool
    {
        $igrejaOrigemId = $this->igrejaOrigemId($membro);

        return $igrejaOrigemId !== null
            && $igrejaDestinoId !== null
            && $igrejaOrigemId !== (int) $igrejaDestinoId;
    }

    public function dataDesligamento(MembresiaMembro $membro): ?\Carbon\Carbon
    {
        $rol = $this->ultimoRolInativo($membro) ?: $this->ultimoRol($membro);
        $data = optional($rol)->dt_exclusao;

        if ($data) {
            return \Carbon\Carbon::parse($data);
        }

        return $membro->deleted_at ? \Carbon\Carbon::parse($membro->deleted_at) : null;
    }

    public function dataDesligamentoFormatada(MembresiaMembro $membro): string
    {
        $data = $this->dataDesligamento($membro);

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

    public function mensagemConfirmacaoInativo(MembresiaMembro $membro): string
    {
        $origem = $this->origemDetalhada($membro);

        return sprintf(
            'Este CPF pertence a %s, que está registrado como membro INATIVO na igreja %s, distrito %s, %s. Deseja continuar com a inclusão?',
            $membro->nome ?: '-',
            $origem['igreja'] ?: '-',
            $origem['distrito'] ?: '-',
            $origem['regiao'] ?: '-'
        );
    }

    public function mensagemDataRecepcaoInvalida(MembresiaMembro $membro): string
    {
        $origem = $this->origemDetalhada($membro);

        return sprintf(
            'Data de recepção não permitida, pois o membro estava ativo na igreja %s até o dia %s. Volte à tela inicial, corrija a data de recepção e continue com a validação.',
            $origem['igreja'] ?: '-',
            $this->dataDesligamentoFormatada($membro)
        );
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
