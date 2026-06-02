<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $now = now();
        $regra = DB::table('regras')->where('nome', 'relatorio-conjuges')->first();

        if (!$regra) {
            $regraId = DB::table('regras')->insertGetId([
                'nome' => 'relatorio-conjuges',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $regraId = $regra->id;
            DB::table('regras')->where('id', $regraId)->update(['updated_at' => $now]);
        }

        $perfilIds = DB::table('perfils')
            ->whereIn('nome', ['Pastor', 'Secretario', 'Administrador do Sistema'])
            ->pluck('id');

        foreach ($perfilIds as $perfilId) {
            $exists = DB::table('perfil_regra')
                ->where('perfil_id', $perfilId)
                ->where('regra_id', $regraId)
                ->exists();

            if (!$exists) {
                DB::table('perfil_regra')->insert([
                    'perfil_id' => $perfilId,
                    'regra_id' => $regraId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down()
    {
        $regra = DB::table('regras')->where('nome', 'relatorio-conjuges')->first();

        if (!$regra) {
            return;
        }

        DB::table('perfil_regra')->where('regra_id', $regra->id)->delete();
        DB::table('regras')->where('id', $regra->id)->delete();
    }
};
