<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $ruleId = DB::table('regras')
            ->where('nome', 'relatorio-clerigos-vinculos')
            ->whereNull('deleted_at')
            ->value('id');

        if (!$ruleId) {
            $ruleId = DB::table('regras')->insertGetId([
                'nome' => 'relatorio-clerigos-vinculos',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        $profileIds = DB::table('perfils')
            ->whereIn('nome', ['Administrador Região', 'Secretário(a) Região', 'Administrador SRA'])
            ->pluck('id');

        foreach ($profileIds as $profileId) {
            DB::table('perfil_regra')->updateOrInsert(
                ['perfil_id' => $profileId, 'regra_id' => $ruleId],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    public function down(): void
    {
        $ruleId = DB::table('regras')
            ->where('nome', 'relatorio-clerigos-vinculos')
            ->value('id');

        if (!$ruleId) {
            return;
        }

        $profileIds = DB::table('perfils')
            ->whereIn('nome', ['Secretário(a) Região', 'Administrador SRA'])
            ->pluck('id');

        DB::table('perfil_regra')
            ->where('regra_id', $ruleId)
            ->whereIn('perfil_id', $profileIds)
            ->delete();
    }
};
