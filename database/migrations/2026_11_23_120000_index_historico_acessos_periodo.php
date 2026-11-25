<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gestao_acessos_sessoes') && !$this->hasIndex('gestao_acessos_sessoes', 'gestao_sessoes_login_idx')) {
            Schema::table('gestao_acessos_sessoes', function (Blueprint $table) {
                $table->index('login_em', 'gestao_sessoes_login_idx');
            });
        }
        if (Schema::hasTable('gestao_acessos_sessao_perfis') && !$this->hasIndex('gestao_acessos_sessao_perfis', 'gestao_perfis_regiao_instituicao_idx')) {
            Schema::table('gestao_acessos_sessao_perfis', function (Blueprint $table) {
                $table->index(['regiao_id', 'instituicao_id', 'sessao_id'], 'gestao_perfis_regiao_instituicao_idx');
            });
        }
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $auditTable = config('audit.drivers.database.table', 'audits');
        if (!$this->hasIndex($auditTable, 'audits_login_periodo_idx', $connection)) {
            Schema::connection($connection)->table($auditTable, function (Blueprint $table) {
                $table->index(['event', 'user_type', 'created_at'], 'audits_login_periodo_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('gestao_acessos_sessoes')) {
            Schema::table('gestao_acessos_sessoes', fn (Blueprint $table) => $table->dropIndex('gestao_sessoes_login_idx'));
        }
        if (Schema::hasTable('gestao_acessos_sessao_perfis')) {
            Schema::table('gestao_acessos_sessao_perfis', fn (Blueprint $table) => $table->dropIndex('gestao_perfis_regiao_instituicao_idx'));
        }
        Schema::connection(config('audit.drivers.database.connection', config('database.default')))
            ->table(config('audit.drivers.database.table', 'audits'), fn (Blueprint $table) => $table->dropIndex('audits_login_periodo_idx'));
    }

    private function hasIndex(string $table, string $index, ?string $connection = null): bool
    {
        $db = DB::connection($connection);
        return $db->table('information_schema.statistics')->where('table_schema', $db->getDatabaseName())
            ->where('table_name', $table)->where('index_name', $index)->exists();
    }
};
