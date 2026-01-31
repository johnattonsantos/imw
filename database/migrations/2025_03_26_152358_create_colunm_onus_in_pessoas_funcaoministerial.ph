<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('pessoas_funcaoministerial', function (Blueprint $table) {
            $table->boolean('onus')->default(false); // Adiciona a coluna com valor default 'false'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('pessoas_funcaoministerial', function (Blueprint $table) {
            $table->dropColumn('onus');
        });
    }
};
