<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evento_inscricoes', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->after('cpf')->unique('evento_inscricoes_qr_token_unique');
        });

        DB::table('evento_inscricoes')
            ->whereNull('qr_token')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($inscricao) {
                DB::table('evento_inscricoes')
                    ->where('id', $inscricao->id)
                    ->update(['qr_token' => (string) Str::uuid()]);
            });

        Schema::create('evento_inscricao_movimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_inscricao_id')->constrained('evento_inscricoes')->cascadeOnDelete();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->string('tipo', 10);
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('registrado_em')->useCurrent();
            $table->string('observacoes', 180)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['evento_id', 'registrado_em'], 'evento_movimentos_evento_registrado_index');
            $table->index(['evento_inscricao_id', 'registrado_em'], 'evento_movimentos_inscricao_registrado_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_inscricao_movimentos');

        Schema::table('evento_inscricoes', function (Blueprint $table) {
            $table->dropUnique('evento_inscricoes_qr_token_unique');
            $table->dropColumn('qr_token');
        });
    }
};
