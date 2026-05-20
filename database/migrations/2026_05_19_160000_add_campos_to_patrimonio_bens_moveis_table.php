<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_bens_moveis', function (Blueprint $table) {
            $table->string('codigo_patrimonial', 60)->nullable()->unique()->after('id');
            $table->string('placa_patrimonial', 60)->nullable()->after('codigo_patrimonial');
            $table->string('nome', 180)->after('placa_patrimonial');
            $table->string('categoria', 120)->nullable()->after('nome');
            $table->text('descricao')->nullable()->after('categoria');
            $table->string('estado_conservacao', 60)->nullable()->after('descricao');
            $table->string('localizacao', 180)->nullable()->after('estado_conservacao');
            $table->string('responsavel', 180)->nullable()->after('localizacao');
            $table->date('data_aquisicao')->nullable()->after('responsavel');
            $table->decimal('valor_aquisicao', 15, 2)->nullable()->after('data_aquisicao');
            $table->decimal('valor_residual', 15, 2)->nullable()->after('valor_aquisicao');
            $table->unsignedSmallInteger('vida_util')->nullable()->after('valor_residual');
            $table->string('natureza_comprobatoria', 120)->nullable()->after('vida_util');
            $table->string('numero_documento', 120)->nullable()->after('natureza_comprobatoria');
            $table->string('fornecedor_doador', 180)->nullable()->after('numero_documento');
            $table->string('status', 60)->default('ativo')->after('fornecedor_doador');
            $table->text('observacoes')->nullable()->after('status');
            $table->text('qr_code_patrimonial')->nullable()->after('observacoes');
        });
    }

    public function down(): void
    {
        Schema::table('patrimonio_bens_moveis', function (Blueprint $table) {
            $table->dropUnique('patrimonio_bens_moveis_codigo_patrimonial_unique');
            $table->dropColumn([
                'codigo_patrimonial',
                'placa_patrimonial',
                'nome',
                'categoria',
                'descricao',
                'estado_conservacao',
                'localizacao',
                'responsavel',
                'data_aquisicao',
                'valor_aquisicao',
                'valor_residual',
                'vida_util',
                'natureza_comprobatoria',
                'numero_documento',
                'fornecedor_doador',
                'status',
                'observacoes',
                'qr_code_patrimonial',
            ]);
        });
    }
};
