<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_imoveis', function (Blueprint $table) {
            if (! Schema::hasColumn('patrimonio_imoveis', 'cnpj_utilizado')) {
                $table->string('cnpj_utilizado', 18)->nullable()->after('situacao_tributaria');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patrimonio_imoveis', function (Blueprint $table) {
            if (Schema::hasColumn('patrimonio_imoveis', 'cnpj_utilizado')) {
                $table->dropColumn('cnpj_utilizado');
            }
        });
    }
};
