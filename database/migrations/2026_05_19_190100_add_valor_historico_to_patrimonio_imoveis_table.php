<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimonio_imoveis', function (Blueprint $table) {
            $table->decimal('valor_historico', 15, 2)->default(0)->after('igreja_id');
        });
    }

    public function down(): void
    {
        Schema::table('patrimonio_imoveis', function (Blueprint $table) {
            $table->dropColumn('valor_historico');
        });
    }
};
