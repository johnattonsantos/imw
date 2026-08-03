<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE ebd_turmas DROP FOREIGN KEY ebd_turmas_professor_id_foreign');
        DB::statement('ALTER TABLE ebd_turmas MODIFY professor_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE ebd_turmas ADD CONSTRAINT ebd_turmas_professor_id_foreign FOREIGN KEY (professor_id) REFERENCES ebd_professores(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $defaultProfessorId = DB::table('ebd_professores')->min('id');

        if ($defaultProfessorId !== null) {
            DB::table('ebd_turmas')->whereNull('professor_id')->update(['professor_id' => $defaultProfessorId]);
        }

        DB::statement('ALTER TABLE ebd_turmas DROP FOREIGN KEY ebd_turmas_professor_id_foreign');
        DB::statement('ALTER TABLE ebd_turmas MODIFY professor_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE ebd_turmas ADD CONSTRAINT ebd_turmas_professor_id_foreign FOREIGN KEY (professor_id) REFERENCES ebd_professores(id)');
    }
};
