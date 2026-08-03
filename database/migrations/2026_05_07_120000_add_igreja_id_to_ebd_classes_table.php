<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebd_classes', function (Blueprint $table) {
            $table->foreignId('igreja_id')->nullable()->after('id')->constrained('instituicoes_instituicoes');
        });
    }

    public function down(): void
    {
        Schema::table('ebd_classes', function (Blueprint $table) {
            $table->dropForeign(['igreja_id']);
            $table->dropColumn('igreja_id');
        });
    }
};
