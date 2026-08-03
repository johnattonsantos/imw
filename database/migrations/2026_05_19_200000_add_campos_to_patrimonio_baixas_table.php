<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_baixas', function (Blueprint $table) {
            $table->string('motivo', 180)->nullable()->after('bem_movel_id');
            $table->date('data_baixa')->nullable()->after('motivo');
            $table->string('responsavel', 180)->nullable()->after('data_baixa');
            $table->string('documento_comprobatorio', 255)->nullable()->after('responsavel');
            $table->text('observacoes')->nullable()->after('documento_comprobatorio');

            $table->index('data_baixa', 'patrimonio_baixas_data_baixa_index');
        });
    }

    public function down(): void
    {
        Schema::table('patrimonio_baixas', function (Blueprint $table) {
            $table->dropIndex('patrimonio_baixas_data_baixa_index');
            $table->dropColumn([
                'motivo',
                'data_baixa',
                'responsavel',
                'documento_comprobatorio',
                'observacoes',
            ]);
        });
    }
};
