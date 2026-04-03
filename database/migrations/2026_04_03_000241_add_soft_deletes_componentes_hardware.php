<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona soft delete para permitir restaurar componentes excluídos.
     */
    public function up(): void
    {
        Schema::table('componentes_hardware', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('componentes_hardware', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
