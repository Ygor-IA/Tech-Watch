<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inscricoes_alerta', function (Blueprint $table) {
            $table->timestamp('notificado_em')->nullable()->after('preco_alvo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscricoes_alerta', function (Blueprint $table) {
            $table->dropColumn('notificado_em');
        });
    }
};
