<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicacao_leituras_igrejas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunicacao_id')
                ->constrained('comunicacao')
                ->cascadeOnDelete();
            $table->foreignId('igreja_id')
                ->constrained('instituicoes_instituicoes')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('lido_em')->nullable();
            $table->timestamps();

            $table->unique(['comunicacao_id', 'igreja_id'], 'comunicacao_leituras_igreja_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicacao_leituras_igrejas');
    }
};

