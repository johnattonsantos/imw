<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('regiao_id')->nullable()->after('pessoa_id');
            $table->foreign('regiao_id')
                ->references('id')
                ->on('instituicoes_instituicoes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['regiao_id']);
            $table->dropColumn('regiao_id');
        });
    }
};

