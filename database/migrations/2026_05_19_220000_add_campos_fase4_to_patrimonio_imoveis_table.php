<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_imoveis', function (Blueprint $table) {
            if (! Schema::hasColumn('patrimonio_imoveis', 'codigo_patrimonial')) {
                $table->string('codigo_patrimonial', 60)->nullable()->after('id');
                $table->unique('codigo_patrimonial', 'patrimonio_imoveis_codigo_patrimonial_unique');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'natureza_imovel')) {
                $table->string('natureza_imovel', 120)->nullable()->after('codigo_patrimonial');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'endereco')) {
                $table->string('endereco', 255)->nullable()->after('nome');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'cidade')) {
                $table->string('cidade', 120)->nullable()->after('endereco');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'estado')) {
                $table->string('estado', 2)->nullable()->after('cidade');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'cep')) {
                $table->string('cep', 9)->nullable()->after('estado');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('cep');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'area_total')) {
                $table->decimal('area_total', 15, 2)->nullable()->after('longitude');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'area_construida')) {
                $table->decimal('area_construida', 15, 2)->nullable()->after('area_total');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'iptu_itr')) {
                $table->string('iptu_itr', 120)->nullable()->after('area_construida');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'inscricao_municipal_rural')) {
                $table->string('inscricao_municipal_rural', 180)->nullable()->after('iptu_itr');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'situacao_tributaria')) {
                $table->string('situacao_tributaria', 120)->nullable()->after('valor_mercado');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'numero_matricula')) {
                $table->string('numero_matricula', 120)->nullable()->after('status_titularidade');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'cartorio')) {
                $table->string('cartorio', 180)->nullable()->after('numero_matricula');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'tipo_titulo')) {
                $table->string('tipo_titulo', 120)->nullable()->after('cartorio');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'data_aquisicao_posse')) {
                $table->date('data_aquisicao_posse')->nullable()->after('tipo_titulo');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'possui_escritura_registrada')) {
                $table->boolean('possui_escritura_registrada')->default(false)->after('data_aquisicao_posse');
            }

            if (! Schema::hasColumn('patrimonio_imoveis', 'observacoes_juridicas')) {
                $table->text('observacoes_juridicas')->nullable()->after('regularizacao_pendente');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patrimonio_imoveis', function (Blueprint $table) {
            if (Schema::hasColumn('patrimonio_imoveis', 'observacoes_juridicas')) {
                $table->dropColumn('observacoes_juridicas');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'possui_escritura_registrada')) {
                $table->dropColumn('possui_escritura_registrada');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'data_aquisicao_posse')) {
                $table->dropColumn('data_aquisicao_posse');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'tipo_titulo')) {
                $table->dropColumn('tipo_titulo');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'cartorio')) {
                $table->dropColumn('cartorio');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'numero_matricula')) {
                $table->dropColumn('numero_matricula');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'situacao_tributaria')) {
                $table->dropColumn('situacao_tributaria');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'inscricao_municipal_rural')) {
                $table->dropColumn('inscricao_municipal_rural');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'iptu_itr')) {
                $table->dropColumn('iptu_itr');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'area_construida')) {
                $table->dropColumn('area_construida');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'area_total')) {
                $table->dropColumn('area_total');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'longitude')) {
                $table->dropColumn('longitude');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'latitude')) {
                $table->dropColumn('latitude');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'cep')) {
                $table->dropColumn('cep');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'estado')) {
                $table->dropColumn('estado');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'cidade')) {
                $table->dropColumn('cidade');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'endereco')) {
                $table->dropColumn('endereco');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'natureza_imovel')) {
                $table->dropColumn('natureza_imovel');
            }

            if (Schema::hasColumn('patrimonio_imoveis', 'codigo_patrimonial')) {
                $table->dropColumn('codigo_patrimonial');
            }
        });
    }
};
