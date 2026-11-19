<?php

namespace App\Services\ServiceRegiaoRelatorios;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrarAcessoService
{
    public function registrar(Request $request): void
    {
        $user = $request->user();
        $session = $request->session()->get('session_perfil');
        if (!$user || !$session || !$session->perfil_id || !$session->instituicao_id) {
            return;
        }
        $agora = now();
        $key = 'gestao_acessos.'.(int) $user->id.'.'.(int) $session->perfil_id.'.'.(int) $session->instituicao_id;
        $ultimo = $request->session()->get($key);
        if ($ultimo && substr($ultimo, 0, 10) === $agora->toDateString() && $agora->diffInSeconds($ultimo) < 300) {
            return;
        }

        // Resolve ownership from the database, not profile names or hierarchy supplied by the browser.
        $vinculo = DB::table('perfil_user as pu')
            ->join('perfils as p', 'p.id', '=', 'pu.perfil_id')
            ->join('instituicoes_instituicoes as i', 'i.id', '=', 'pu.instituicao_id')
            ->leftJoin('instituicoes_instituicoes as pai', 'pai.id', '=', 'i.instituicao_pai_id')
            ->where('pu.user_id', $user->id)->where('pu.perfil_id', $session->perfil_id)
            ->where('pu.instituicao_id', $session->instituicao_id)
            ->whereNull('p.deleted_at')->whereNull('i.deleted_at')
            ->whereIn('i.tipo_instituicao_id', [1, 2, 3])
            ->select('p.nome as perfil_nome', 'i.nome as instituicao_nome')
            ->selectRaw('CASE i.tipo_instituicao_id WHEN 3 THEN i.id WHEN 2 THEN i.instituicao_pai_id WHEN 1 THEN pai.instituicao_pai_id END as regiao_id')
            ->first();
        if (!$vinculo || !$vinculo->regiao_id) {
            return;
        }

        app(SessaoAcessoService::class)->registrarPerfil($request, $vinculo);

        $identity = ['user_id' => $user->id, 'perfil_id' => $session->perfil_id,
            'instituicao_id' => $session->instituicao_id, 'data_acesso' => $agora->toDateString()];
        DB::table('gestao_acessos')->insertOrIgnore($identity + [
            'regiao_id' => $vinculo->regiao_id, 'primeiro_acesso' => $agora, 'ultimo_acesso' => $agora,
        ]);
        DB::table('gestao_acessos')->where($identity)->update(['ultimo_acesso' => $agora]);
        $request->session()->put($key, $agora->toDateTimeString());
    }
}
