<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona índices de performance para queries frequentes.
     */
    public function up(): void
    {
        // Índice na coluna registrado_em — usada em orderBy no gráfico e verificação
        Schema::table('historicos_preco', function (Blueprint $table) {
            $table->index('registrado_em');
        });

        // Índices na tabela de alertas — usados por scopePendentes() e buscas por email
        Schema::table('inscricoes_alerta', function (Blueprint $table) {
            $table->index('notificado_em');
            $table->index('email_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historicos_preco', function (Blueprint $table) {
            $table->dropIndex(['registrado_em']);
        });

        Schema::table('inscricoes_alerta', function (Blueprint $table) {
            $table->dropIndex(['notificado_em']);
            $table->dropIndex(['email_usuario']);
        });
    }
};
