<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('instituicoes_instituicoes', 'data_encerramento')) {
            return;
        }

        Schema::table('instituicoes_instituicoes', function (Blueprint $table) {
            $table->date('data_encerramento')->nullable()->after('data_abertura');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('instituicoes_instituicoes', 'data_encerramento')) {
            return;
        }

        Schema::table('instituicoes_instituicoes', function (Blueprint $table) {
            $table->dropColumn('data_encerramento');
        });
    }
};
