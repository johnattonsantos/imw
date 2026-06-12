<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $now = now();
        $rule = DB::table('regras')->where('nome', 'relatorio-membros-por-bairro')->first();

        if ($rule) {
            $ruleId = $rule->id;
            DB::table('regras')->where('id', $ruleId)->update([
                'deleted_at' => null,
                'updated_at' => $now,
            ]);
        } else {
            $ruleId = DB::table('regras')->insertGetId([
                'nome' => 'relatorio-membros-por-bairro',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        $profileIds = DB::table('perfils')
            ->whereIn('nome', ['Pastor', 'Secretario', 'Administrador do Sistema'])
            ->pluck('id');

        foreach ($profileIds as $profileId) {
            $exists = DB::table('perfil_regra')
                ->where('perfil_id', $profileId)
                ->where('regra_id', $ruleId)
                ->exists();

            if (! $exists) {
                DB::table('perfil_regra')->insert([
                    'perfil_id' => $profileId,
                    'regra_id' => $ruleId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down()
    {
        $rule = DB::table('regras')->where('nome', 'relatorio-membros-por-bairro')->first();

        if (! $rule) {
            return;
        }

        $profileIds = DB::table('perfils')
            ->whereIn('nome', ['Pastor', 'Secretario', 'Administrador do Sistema'])
            ->pluck('id');

        DB::table('perfil_regra')
            ->where('regra_id', $rule->id)
            ->whereIn('perfil_id', $profileIds)
            ->delete();
    }
};
