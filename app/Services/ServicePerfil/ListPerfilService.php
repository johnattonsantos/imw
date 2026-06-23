<?php

namespace App\Services\ServicePerfil;

use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\PessoasPessoa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListPerfilService
{
    public function execute()
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar essa página.');
        }
        return $usuario;
    }

    public function carteiraDigital()
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar essa página.');
        }

        if (!$usuario->pessoa_id) {
            return ['pessoa_id' => null];
        }

        $pessoa = PessoasPessoa::where('id', $usuario->pessoa_id)->first();
        if (!$pessoa) {
            return ['pessoa_id' => null];
        }

        $instituicao = $this->getLoggedRegionInstitution($pessoa);
        $superintendenteRegional = $this->getActiveRegionalSuperintendent($instituicao);

        $pessoa['nome_regiao'] = optional($instituicao)->nome ?? '';
        $pessoa['nome_regiao_formatado'] = $this->formatRegionName($pessoa['nome_regiao']);
        $pessoa['telefone_sede_administrativa'] = $this->formatPersonPhone($superintendenteRegional);
        $pessoa['superintendente_regional_nome'] = $this->formatPersonName(optional($superintendenteRegional)->nome);

        if ($pessoa->foto) {
            $pessoa->foto = $this->resolveFotoUrl((string) $pessoa->foto);
        }

        $pessoa['pessoa_id'] = $usuario->pessoa_id;
        return $pessoa;
    }

    private function resolveFotoUrl(string $foto): string
    {
        if (Str::startsWith($foto, ['http://', 'https://'])) {
            return $foto;
        }

        if (Str::startsWith($foto, ['/storage/', 'storage/'])) {
            return Str::startsWith($foto, '/') ? $foto : '/' . ltrim($foto, '/');
        }

        if (! $this->hasS3Credentials()) {
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

    private function hasS3Credentials(): bool
    {
        return filled(Config::get('filesystems.disks.s3.key'))
            && filled(Config::get('filesystems.disks.s3.secret'))
            && filled(Config::get('filesystems.disks.s3.region'))
            && filled(Config::get('filesystems.disks.s3.bucket'));
    }

    private function formatRegionName(?string $regionName): string
    {
        $regionName = trim((string) $regionName);

        if ($regionName === '') {
            return '';
        }

        if (preg_match('/\d+/', $regionName, $matches)) {
            return $matches[0] . 'ª Região';
        }

        return mb_convert_case($regionName, MB_CASE_TITLE, 'UTF-8');
    }

    private function getLoggedRegionInstitution(PessoasPessoa $pessoa): ?InstituicoesInstituicao
    {
        $sessionPerfil = session('session_perfil');
        $regiaoId = (int) data_get($sessionPerfil, 'instituicoes.regiao.id', 0);

        if ($regiaoId <= 0) {
            $instituicaoId = (int) data_get($sessionPerfil, 'instituicao_id', 0);
            $regiaoId = $this->resolveRegionIdByInstitutionId($instituicaoId);
        }

        if ($regiaoId <= 0 && !empty($pessoa->regiao_id)) {
            $regiaoId = (int) $pessoa->regiao_id;
        }

        return $regiaoId > 0
            ? InstituicoesInstituicao::query()->where('id', $regiaoId)->first()
            : null;
    }

    private function resolveRegionIdByInstitutionId(int $instituicaoId): int
    {
        $currentId = $instituicaoId;
        $maxDepth = 10;

        while ($currentId > 0 && $maxDepth-- > 0) {
            $instituicao = InstituicoesInstituicao::query()
                ->select(['id', 'tipo_instituicao_id', 'instituicao_pai_id', 'regiao_id'])
                ->find($currentId);

            if (!$instituicao) {
                return 0;
            }

            if ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::REGIAO) {
                return (int) $instituicao->id;
            }

            if (!empty($instituicao->regiao_id)) {
                return (int) $instituicao->regiao_id;
            }

            $currentId = (int) ($instituicao->instituicao_pai_id ?? 0);
        }

        return 0;
    }

    private function getActiveRegionalSuperintendent(?InstituicoesInstituicao $instituicao): ?object
    {
        if (!$instituicao) {
            return null;
        }

        return DB::table('pessoas_pessoas as pp')
            ->join('pessoas_nomeacoes as pn', 'pn.pessoa_id', '=', 'pp.id')
            ->join('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->select([
                'pp.nome',
                'pp.telefone_preferencial',
                'pp.telefone_alternativo',
            ])
            ->where('pp.tipo', 'CLE')
            ->where('pp.situacao_id', 1)
            ->where('pp.regiao_id', $instituicao->id)
            ->where('pf.funcao', 'Superintendente Regional')
            ->whereNull('pn.data_termino')
            ->whereNull('pn.deleted_at')
            ->whereNull('pp.deleted_at')
            ->orderByDesc('pn.data_nomeacao')
            ->first();
    }

    private function formatPersonName(?string $nome): string
    {
        $nome = trim((string) $nome);

        return $nome !== '' ? mb_convert_case($nome, MB_CASE_TITLE, 'UTF-8') : '';
    }

    private function formatPersonPhone(?object $pessoa): string
    {
        if (!$pessoa) {
            return '';
        }

        $telefone = preg_replace(
            '/\D+/',
            '',
            (string) ($pessoa->telefone_preferencial ?: $pessoa->telefone_alternativo)
        );

        if ($telefone === '') {
            return '';
        }

        return formatarTelefone($telefone);
    }
}
