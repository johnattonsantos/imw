<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE pessoas_pessoas MODIFY residencia_propria_fgts TINYINT(1) NULL');
    }

    public function down(): void
    {
        DB::table('pessoas_pessoas')
            ->whereNull('residencia_propria_fgts')
            ->update(['residencia_propria_fgts' => 0]);

        DB::statement('ALTER TABLE pessoas_pessoas MODIFY residencia_propria_fgts TINYINT(1) NOT NULL');
    }
};
