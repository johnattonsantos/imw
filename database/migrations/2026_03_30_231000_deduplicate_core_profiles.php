<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $this->consolidateProfile('administrador_sistema');
            $this->consolidateProfile('crie');
        });
    }

    public function down(): void
    {
        // Sem rollback seguro para deduplicação de dados.
    }

    private function consolidateProfile(string $nome): void
    {
        $ids = DB::table('perfils')
            ->where('nome', $nome)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if (count($ids) <= 1) {
            return;
        }

        $keepId = (int) array_shift($ids);
        $duplicateIds = array_map('intval', $ids);

        DB::table('perfil_user')
            ->whereIn('perfil_id', $duplicateIds)
            ->update(['perfil_id' => $keepId]);

        DB::table('perfil_regra')
            ->whereIn('perfil_id', $duplicateIds)
            ->update(['perfil_id' => $keepId]);

        DB::statement("
            DELETE pu1
            FROM perfil_user pu1
            JOIN perfil_user pu2
              ON pu1.user_id = pu2.user_id
             AND pu1.perfil_id = pu2.perfil_id
             AND pu1.instituicao_id = pu2.instituicao_id
             AND pu1.id > pu2.id
        ");

        DB::statement("
            DELETE pr1
            FROM perfil_regra pr1
            JOIN perfil_regra pr2
              ON pr1.perfil_id = pr2.perfil_id
             AND pr1.regra_id = pr2.regra_id
             AND pr1.id > pr2.id
        ");

        DB::table('perfils')->whereIn('id', $duplicateIds)->delete();
    }
};

