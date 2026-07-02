<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ebd_turmas', 'congregacao_id')) {
            Schema::table('ebd_turmas', function (Blueprint $table) {
                $table->foreignId('congregacao_id')
                    ->nullable()
                    ->after('professor_id')
                    ->constrained('congregacoes_congregacoes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ebd_turmas', 'congregacao_id')) {
            Schema::table('ebd_turmas', function (Blueprint $table) {
                $table->dropConstrainedForeignId('congregacao_id');
            });
        }
    }
};

