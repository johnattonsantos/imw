<?php

namespace App\Services\ServicePerfil;

use App\Models\InstituicoesInstituicao;
use App\Models\MembresiaSetor;
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

        $instituicao = null;
        if (!empty($pessoa->regiao_id)) {
            $instituicao = InstituicoesInstituicao::where('id', $pessoa->regiao_id)->first();
        }

        $pessoa['nome_regiao'] = optional($instituicao)->nome ?? '';
        $pessoa['nome_regiao_formatado'] = $this->formatRegionName($pessoa['nome_regiao']);
        $pessoa['telefone_sede_administrativa'] = $this->formatInstitutionPhone($instituicao);
        $pessoa['superintendente_regional_nome'] = $this->getActiveRegionalSuperintendentName($instituicao);

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

    private function getActiveRegionalSuperintendentName(?InstituicoesInstituicao $instituicao): string
    {
        if (!$instituicao) {
            return '';
        }

        $nome = DB::table('pessoas_nomeacoes as pn')
            ->join('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->join('pessoas_pessoas as pp', 'pp.id', '=', 'pn.pessoa_id')
            ->leftJoin('instituicoes_instituicoes as inst', 'inst.id', '=', 'pn.instituicao_id')
            ->where(function ($query) use ($instituicao) {
                $query->where('pn.instituicao_id', $instituicao->id)
                    ->orWhere('pn.hist_regiao_id', $instituicao->id)
                    ->orWhere('inst.instituicao_pai_id', $instituicao->id)
                    ->orWhere('inst.regiao_id', $instituicao->id);
            })
            ->where('pf.funcao', 'Superintendente Regional')
            ->whereNull('pn.data_termino')
            ->whereNull('pn.deleted_at')
            ->whereNull('pp.deleted_at')
            ->orderByDesc('pn.data_nomeacao')
            ->value('pp.nome');

        return $nome ? mb_convert_case($nome, MB_CASE_TITLE, 'UTF-8') : '';
    }

    private function formatInstitutionPhone(?InstituicoesInstituicao $instituicao): string
    {
        if (!$instituicao || empty($instituicao->telefone)) {
            return '';
        }

        $telefone = preg_replace('/\D+/', '', (string) $instituicao->telefone);
        $ddd = preg_replace('/\D+/', '', (string) $instituicao->ddd);

        if ($ddd !== '' && strlen($telefone) <= 9) {
            $telefone = $ddd . $telefone;
        }

        return formatarTelefone($telefone);
    }
}
