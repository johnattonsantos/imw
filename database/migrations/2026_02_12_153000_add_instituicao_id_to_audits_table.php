<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('audit.drivers.database.table', 'audits');
        $connection = config('audit.drivers.database.connection', config('database.default'));

        Schema::connection($connection)->table($table, function (Blueprint $table) {
            $table->unsignedBigInteger('instituicao_id')->nullable()->after('user_id');
            $table->index('instituicao_id');
        });
    }

    public function down(): void
    {
        $table = config('audit.drivers.database.table', 'audits');
        $connection = config('audit.drivers.database.connection', config('database.default'));

        Schema::connection($connection)->table($table, function (Blueprint $table) {
            $table->dropIndex(['instituicao_id']);
            $table->dropColumn('instituicao_id');
        });
    }
};
