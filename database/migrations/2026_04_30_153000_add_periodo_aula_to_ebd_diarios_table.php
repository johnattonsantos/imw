<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebd_diarios', function (Blueprint $table) {
            $table->enum('periodo_aula', ['manha', 'noite'])->nullable()->after('hora_fim');
        });
    }

    public function down(): void
    {
        Schema::table('ebd_diarios', function (Blueprint $table) {
            $table->dropColumn('periodo_aula');
        });
    }
};

