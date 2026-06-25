<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_riscos_juridicos', function (Blueprint $table) {
            $table->boolean('possui_onus')->default(false)->after('imovel_id');
            $table->string('tipo_onus', 120)->nullable()->after('possui_onus');
            $table->text('descricao')->nullable()->after('tipo_onus');
            $table->string('nivel_risco', 20)->default('baixo')->after('descricao');
            $table->date('data_identificacao')->nullable()->after('nivel_risco');
            $table->text('providencia_recomendada')->nullable()->after('data_identificacao');
            $table->string('status', 60)->default('aberto')->after('providencia_recomendada');

            $table->index('nivel_risco', 'patrimonio_riscos_nivel_risco_index');
            $table->index('status', 'patrimonio_riscos_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('patrimonio_riscos_juridicos', function (Blueprint $table) {
            $table->dropIndex('patrimonio_riscos_nivel_risco_index');
            $table->dropIndex('patrimonio_riscos_status_index');

            $table->dropColumn([
                'possui_onus',
                'tipo_onus',
                'descricao',
                'nivel_risco',
                'data_identificacao',
                'providencia_recomendada',
                'status',
            ]);
        });
    }
};
