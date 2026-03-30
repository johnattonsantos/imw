<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('perfils')
            ->whereIn('nome', ['Administrador do Sistema', 'administrador do sistema'])
            ->update(['nome' => 'administrador_sistema']);

        DB::table('perfils')
            ->whereIn('nome', ['CRIE', 'Crie'])
            ->update(['nome' => 'crie']);
    }

    public function down(): void
    {
        DB::table('perfils')
            ->where('nome', 'administrador_sistema')
            ->update(['nome' => 'Administrador do Sistema']);

        DB::table('perfils')
            ->where('nome', 'crie')
            ->update(['nome' => 'CRIE']);
    }
};

