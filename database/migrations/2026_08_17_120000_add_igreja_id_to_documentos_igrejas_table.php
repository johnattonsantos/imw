<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('documentos_igrejas', 'igreja_id')) {
            return;
        }

        Schema::table('documentos_igrejas', function (Blueprint $table) {
            $table->unsignedBigInteger('igreja_id')->nullable()->after('regiao_id');
            $table->index('igreja_id', 'documentos_igrejas_igreja_id_index');
            $table->foreign('igreja_id', 'documentos_igrejas_igreja_id_foreign')
                ->references('id')
                ->on('instituicoes_instituicoes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('documentos_igrejas', 'igreja_id')) {
            return;
        }

        Schema::table('documentos_igrejas', function (Blueprint $table) {
            $table->dropForeign('documentos_igrejas_igreja_id_foreign');
            $table->dropIndex('documentos_igrejas_igreja_id_index');
            $table->dropColumn('igreja_id');
        });
    }
};
