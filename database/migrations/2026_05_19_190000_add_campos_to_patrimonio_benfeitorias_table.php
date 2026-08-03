<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_benfeitorias', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('imovel_id');
            $table->date('data')->nullable()->after('descricao');
            $table->decimal('valor_investido', 15, 2)->default(0)->after('data');
            $table->string('responsavel', 180)->nullable()->after('valor_investido');
            $table->string('documento_anexo', 255)->nullable()->after('responsavel');
            $table->text('observacoes')->nullable()->after('documento_anexo');

            $table->index('data', 'patrimonio_benfeitorias_data_index');
        });
    }

    public function down(): void
    {
        Schema::table('patrimonio_benfeitorias', function (Blueprint $table) {
            $table->dropIndex('patrimonio_benfeitorias_data_index');
            $table->dropColumn([
                'descricao',
                'data',
                'valor_investido',
                'responsavel',
                'documento_anexo',
                'observacoes',
            ]);
        });
    }
};
