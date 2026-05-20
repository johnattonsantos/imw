<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_imoveis', function (Blueprint $table) {
            if (! Schema::hasColumn('patrimonio_imoveis', 'nome')) {
                $table->string('nome', 180)->nullable()->after('id');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'valor_venal')) {
                $table->decimal('valor_venal', 15, 2)->nullable()->after('valor_historico');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'valor_mercado')) {
                $table->decimal('valor_mercado', 15, 2)->nullable()->after('valor_venal');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'status_titularidade')) {
                $table->string('status_titularidade', 80)->nullable()->after('valor_mercado');
                $table->index('status_titularidade', 'patrimonio_imoveis_status_titularidade_index');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'regularizacao_pendente')) {
                $table->boolean('regularizacao_pendente')->default(false)->after('status_titularidade');
                $table->index('regularizacao_pendente', 'patrimonio_imoveis_regularizacao_pendente_index');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'avcb_validade')) {
                $table->date('avcb_validade')->nullable()->after('regularizacao_pendente');
                $table->index('avcb_validade', 'patrimonio_imoveis_avcb_validade_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patrimonio_imoveis', function (Blueprint $table) {
            if (Schema::hasColumn('patrimonio_imoveis', 'avcb_validade')) {
                $table->dropIndex('patrimonio_imoveis_avcb_validade_index');
                $table->dropColumn('avcb_validade');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'regularizacao_pendente')) {
                $table->dropIndex('patrimonio_imoveis_regularizacao_pendente_index');
                $table->dropColumn('regularizacao_pendente');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'status_titularidade')) {
                $table->dropIndex('patrimonio_imoveis_status_titularidade_index');
                $table->dropColumn('status_titularidade');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'valor_mercado')) {
                $table->dropColumn('valor_mercado');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'valor_venal')) {
                $table->dropColumn('valor_venal');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'nome')) {
                $table->dropColumn('nome');
            }
        });
    }
};
