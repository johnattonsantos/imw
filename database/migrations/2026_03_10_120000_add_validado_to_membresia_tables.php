<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membresia_migracao', function (Blueprint $table) {
            $table->boolean('validado')->default(false)->after('has_errors');
        });

        Schema::table('membresia_membros', function (Blueprint $table) {
            $table->boolean('validado')->default(false)->after('has_errors');
        });
    }

    public function down(): void
    {
        Schema::table('membresia_migracao', function (Blueprint $table) {
            $table->dropColumn('validado');
        });

        Schema::table('membresia_membros', function (Blueprint $table) {
            $table->dropColumn('validado');
        });
    }
};
