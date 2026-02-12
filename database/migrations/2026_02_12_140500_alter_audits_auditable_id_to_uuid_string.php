<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('audit.drivers.database.table', 'audits');
        $connection = config('audit.drivers.database.connection', config('database.default'));

        DB::connection($connection)->statement(
            sprintf('ALTER TABLE `%s` MODIFY `auditable_id` VARCHAR(36) NOT NULL', $table)
        );
    }

    public function down(): void
    {
        $table = config('audit.drivers.database.table', 'audits');
        $connection = config('audit.drivers.database.connection', config('database.default'));

        DB::connection($connection)->statement(
            sprintf('ALTER TABLE `%s` MODIFY `auditable_id` BIGINT UNSIGNED NOT NULL', $table)
        );
    }
};
