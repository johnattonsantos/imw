<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MensagemPastorService
{
    public function registrar(Request $request): bool
    {
        $loginId = $request->session()->get('pastor_login_pendente');
        $perfil = $request->session()->get('session_perfil');
        if (!$loginId || !$request->user() || mb_strtolower(trim((string) data_get($perfil, 'perfil_nome'))) !== 'pastor') {
            return false;
        }

        $userId = (int) $request->user()->id;
        try {
            $autorizado = DB::table('perfil_user as pu')
                ->join('perfils as p', 'p.id', '=', 'pu.perfil_id')
                ->join('instituicoes_instituicoes as i', 'i.id', '=', 'pu.instituicao_id')
                ->where('pu.user_id', $userId)
                ->where('pu.perfil_id', data_get($perfil, 'perfil_id'))
                ->where('pu.instituicao_id', data_get($perfil, 'instituicao_id'))
                ->where('p.nome', 'Pastor')
                ->where('i.tipo_instituicao_id', 1)
                ->exists();
            if (!$autorizado) {
                return false;
            }

            $exibir = DB::transaction(function () use ($userId, $loginId) {
                // Serialize logins from different devices before counting the milestone.
                DB::table('users')->where('id', $userId)->lockForUpdate()->first();
                $inserido = DB::table('pastor_acessos_motivacionais')->insertOrIgnore([
                    'login_id' => $loginId, 'user_id' => $userId, 'created_at' => now(),
                ]);
                if (!$inserido) {
                    return false;
                }

                return DB::table('pastor_acessos_motivacionais')->where('user_id', $userId)->count() % 15 === 0;
            }, 3);
            $request->session()->forget('pastor_login_pendente');

            return $exibir;
        } catch (\Throwable $e) {
            // A missing migration or unavailable database must not block the dashboard.
            Log::warning('Falha ao registrar acesso para mensagem do pastor.', ['exception' => get_class($e)]);
            return false;
        }
    }
}
