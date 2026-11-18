<?php

namespace App\Services\ServiceRegiaoRelatorios;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SessaoAcessoService
{
    private const KEY = 'gestao_sessao_acesso';

    public function iniciar(Request $request): array
    {
        $request->session()->forget('gestao_acessos');
        $sessao = $this->nova($request, now()->toDateTimeString());
        $this->persistirSemBloquear($sessao);
        return $sessao;
    }

    private function nova(Request $request, ?string $login): array
    {
        // Independent UUID: never persist the actual authentication session token.
        $sessao = ['id' => (string) Str::uuid(), 'user_id' => (int) $request->user()->id,
            'login_em' => $login, 'observada_em' => now()->toDateTimeString()];
        $request->session()->put(self::KEY, $sessao);
        return $sessao;
    }

    public function registrarPerfil(Request $request, object $vinculo): void
    {
        $sessao = $request->session()->get(self::KEY);
        if (!$sessao || (int) $sessao['user_id'] !== (int) $request->user()->id) {
            // Existing/remember-me sessions have no provable login timestamp.
            $sessao = $this->nova($request, null);
        }
        $this->persistir($sessao);
        $perfil = $request->session()->get('session_perfil');
        $identity = ['sessao_id' => $sessao['id'], 'perfil_id' => $perfil->perfil_id, 'instituicao_id' => $perfil->instituicao_id];
        DB::table('gestao_acessos_sessao_perfis')->insertOrIgnore($identity + [
            'regiao_id' => $vinculo->regiao_id, 'usuario_nome' => $request->user()->name ?? '',
            'perfil_nome' => $vinculo->perfil_nome, 'instituicao_nome' => $vinculo->instituicao_nome,
            'primeiro_uso' => now(), 'ultimo_uso' => now(),
        ]);
        DB::table('gestao_acessos_sessao_perfis')->where($identity)->update(['ultimo_uso' => now()]);
    }

    public function finalizar(Request $request): ?string
    {
        $sessao = $request->session()->get(self::KEY);
        if (!$sessao || (int) $sessao['user_id'] !== (int) $request->user()->id) {
            return null;
        }
        try {
            $this->persistir($sessao);
            DB::table('gestao_acessos_sessoes')->where('id', $sessao['id'])->where('user_id', $sessao['user_id'])
                ->whereNull('logout_em')->update(['logout_em' => now()]);
        } catch (\Throwable $e) {
            Log::warning('Falha ao registrar logout na Gestao de Acessos.', ['exception' => get_class($e)]);
        }
        return $sessao['id'];
    }

    private function persistir(array $sessao): void
    {
        DB::table('gestao_acessos_sessoes')->insertOrIgnore($sessao);
    }

    private function persistirSemBloquear(array $sessao): void
    {
        try {
            $this->persistir($sessao);
        } catch (\Throwable $e) {
            Log::warning('Falha ao registrar login na Gestao de Acessos.', ['exception' => get_class($e)]);
        }
    }
}
