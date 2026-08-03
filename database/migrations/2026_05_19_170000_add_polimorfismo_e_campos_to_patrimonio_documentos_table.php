<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_documentos', function (Blueprint $table) {
            $table->string('nome', 180)->nullable()->after('id');
            $table->string('tipo', 120)->nullable()->after('nome');
            $table->string('arquivo', 255)->nullable()->after('tipo');
            $table->date('data_emissao')->nullable()->after('arquivo');
            $table->date('data_validade')->nullable()->after('data_emissao');
            $table->string('status', 60)->default('vigente')->after('data_validade');
            $table->text('observacoes')->nullable()->after('status');
            $table->unsignedBigInteger('documentavel_id')->nullable()->after('observacoes');
            $table->string('documentavel_type')->nullable()->after('documentavel_id');
            $table->index(['documentavel_type', 'documentavel_id'], 'patrimonio_documentos_documentavel_index');
        });

        // Backfill para não perder vínculos antigos (imovel_id/bem_movel_id)
        DB::table('patrimonio_documentos')
            ->whereNull('documentavel_type')
            ->whereNotNull('imovel_id')
            ->update([
                'documentavel_type' => 'App\\Models\\Patrimonio\\Imovel',
                'documentavel_id' => DB::raw('imovel_id'),
            ]);

        DB::table('patrimonio_documentos')
            ->whereNull('documentavel_type')
            ->whereNotNull('bem_movel_id')
            ->update([
                'documentavel_type' => 'App\\Models\\Patrimonio\\BemMovel',
                'documentavel_id' => DB::raw('bem_movel_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('patrimonio_documentos', function (Blueprint $table) {
            $table->dropIndex('patrimonio_documentos_documentavel_index');
            $table->dropColumn([
                'nome',
                'tipo',
                'arquivo',
                'data_emissao',
                'data_validade',
                'status',
                'observacoes',
                'documentavel_id',
                'documentavel_type',
            ]);
        });
    }
};
