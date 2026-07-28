<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('pessoas_funcaoministerial', 'vinculo')) {
            $afterColumn = Schema::hasColumn('pessoas_funcaoministerial', 'onus') ? 'onus' : 'qtd_prebendas';

            Schema::table('pessoas_funcaoministerial', function (Blueprint $table) use ($afterColumn) {
                $table->string('vinculo', 20)->nullable()->after($afterColumn);
            });
        }

        DB::table('pessoas_funcaoministerial')
            ->whereNull('vinculo')
            ->whereRaw('LOWER(funcao) LIKE ?', ['%integral%'])
            ->update(['vinculo' => 'integral']);

        DB::table('pessoas_funcaoministerial')
            ->whereNull('vinculo')
            ->whereRaw('LOWER(funcao) LIKE ?', ['%parcial%'])
            ->update(['vinculo' => 'parcial']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('pessoas_funcaoministerial', 'vinculo')) {
            Schema::table('pessoas_funcaoministerial', function (Blueprint $table) {
                $table->dropColumn('vinculo');
            });
        }
    }
};
