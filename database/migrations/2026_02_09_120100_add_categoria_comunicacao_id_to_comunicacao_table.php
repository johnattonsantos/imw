<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunicacao', function (Blueprint $table) {
            $table->foreignId('categoria_comunicacao_id')
                ->nullable()
                ->after('instituicao_id')
                ->constrained('categoria_comunicacao')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comunicacao', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_comunicacao_id');
        });
    }
};
