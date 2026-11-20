<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pastor_acessos_motivacionais', function (Blueprint $table) {
            $table->uuid('login_id')->primary();
            $table->unsignedBigInteger('user_id')->index();
            $table->dateTime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pastor_acessos_motivacionais');
    }
};
