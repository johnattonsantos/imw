<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VincularInstituicaoLoginService
{
    public function registrar(Request $request): void
    {
        $auditId = $request->session()->get('audit_login_pendente');
        $perfil = $request->session()->get('session_perfil');
        $user = $request->user();
        if (!$auditId || !$user || !data_get($perfil, 'instituicao_id') || !data_get($perfil, 'perfil_id')) {
            return;
        }

        try {
            $valido = DB::table('perfil_user as pu')
                ->join('perfils as p', 'p.id', '=', 'pu.perfil_id')
                ->join('instituicoes_instituicoes as i', 'i.id', '=', 'pu.instituicao_id')
                ->where('pu.user_id', $user->id)->where('pu.perfil_id', $perfil->perfil_id)
                ->where('pu.instituicao_id', $perfil->instituicao_id)
                ->whereNull('p.deleted_at')->whereNull('i.deleted_at')->exists();
            if (!$valido) {
                return;
            }

            // Enrich only this login, once; profile switches must not rewrite its original institution.
            DB::connection(config('audit.drivers.database.connection', config('database.default')))
                ->table(config('audit.drivers.database.table', 'audits'))
                ->where('id', $auditId)->where('user_id', $user->id)->where('user_type', User::class)
                ->where('event', 'login')->whereNull('instituicao_id')
                ->update(['instituicao_id' => $perfil->instituicao_id, 'updated_at' => now()]);
            $request->session()->forget('audit_login_pendente');
        } catch (\Throwable $e) {
            Log::warning('Falha ao vincular instituicao ao login auditado.', ['exception' => get_class($e)]);
        }
    }
}
