<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('patrimonio_configuracoes', 'igreja_id')) {
            return;
        }

        $database = DB::getDatabaseName();

        $foreignKeys = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', 'patrimonio_configuracoes')
            ->where('COLUMN_NAME', 'igreja_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->pluck('CONSTRAINT_NAME');

        foreach ($foreignKeys as $foreignKeyName) {
            DB::statement("ALTER TABLE `patrimonio_configuracoes` DROP FOREIGN KEY `{$foreignKeyName}`");
        }

        Schema::table('patrimonio_configuracoes', function (Blueprint $table) {
            $table->dropColumn('igreja_id');
        });

        $uniqueExists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', 'patrimonio_configuracoes')
            ->where('INDEX_NAME', 'patrimonio_config_unique_nome_por_tipo')
            ->exists();

        if ($uniqueExists) {
            DB::statement('ALTER TABLE `patrimonio_configuracoes` DROP INDEX `patrimonio_config_unique_nome_por_tipo`');
        }

        DB::statement('ALTER TABLE `patrimonio_configuracoes` ADD UNIQUE `patrimonio_config_unique_nome_por_tipo` (`tipo`, `nome`)');

        $indexTipoExists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', 'patrimonio_configuracoes')
            ->where('INDEX_NAME', 'patrimonio_config_tipo_idx')
            ->exists();

        if (! $indexTipoExists) {
            DB::statement('ALTER TABLE `patrimonio_configuracoes` ADD INDEX `patrimonio_config_tipo_idx` (`tipo`)');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('patrimonio_configuracoes', 'igreja_id')) {
            return;
        }

        $database = DB::getDatabaseName();
        $uniqueExists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', 'patrimonio_configuracoes')
            ->where('INDEX_NAME', 'patrimonio_config_unique_nome_por_tipo')
            ->exists();

        if ($uniqueExists) {
            DB::statement('ALTER TABLE `patrimonio_configuracoes` DROP INDEX `patrimonio_config_unique_nome_por_tipo`');
        }

        Schema::table('patrimonio_configuracoes', function (Blueprint $table) {
            $table->foreignId('igreja_id')
                ->nullable()
                ->after('id')
                ->constrained('instituicoes_instituicoes');

            $table->index(['igreja_id', 'tipo'], 'patrimonio_config_igreja_tipo_idx');
            $table->unique(['igreja_id', 'tipo', 'nome'], 'patrimonio_config_unique_nome_por_tipo');
        });

        $indexTipoExists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', 'patrimonio_configuracoes')
            ->where('INDEX_NAME', 'patrimonio_config_tipo_idx')
            ->exists();

        if ($indexTipoExists) {
            DB::statement('ALTER TABLE `patrimonio_configuracoes` DROP INDEX `patrimonio_config_tipo_idx`');
        }
    }
};
