<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('membresia_migracao') || Schema::hasTable('membresia_rolpermanente_migracao')) {
            return;
        }

        Schema::create('membresia_rolpermanente_migracao', function (Blueprint $table) {
            $table->id();
            $table->string('status', 1);
            $table->integer('numero_rol')->nullable();
            $table->integer('codigo_host')->nullable();
            $table->date('dt_recepcao')->nullable();
            $table->date('dt_exclusao')->nullable();
            $table->unsignedBigInteger('clerigo_id')->nullable();
            $table->unsignedBigInteger('distrito_id')->nullable();
            $table->unsignedBigInteger('igreja_id')->nullable();
            $table->char('membro_id', 36);
            $table->unsignedBigInteger('modo_exclusao_id')->nullable();
            $table->unsignedBigInteger('modo_recepcao_id')->nullable();
            $table->unsignedBigInteger('regiao_id')->nullable();
            $table->unsignedBigInteger('congregacao_id')->nullable();
            $table->boolean('lastrec')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['membro_id', 'lastrec']);
            $table->index('igreja_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('membresia_rolpermanente_migracao');
    }
};
