<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona novos campos à tabela componentes_hardware:
     * - user_id: vincula cada hardware ao usuário que cadastrou
     * - categoria: tipo do componente (GPU, CPU, RAM, etc.)
     * - ativo: permite pausar monitoramento sem excluir
     * - preco_minimo / preco_maximo: rastreia extremos de preço
     */
    public function up(): void
    {
        Schema::table('componentes_hardware', function (Blueprint $table) {
            // Vínculo com o usuário (nullable para não quebrar registros existentes)
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Categoria do hardware
            $table->string('categoria', 50)
                  ->nullable()
                  ->after('nome')
                  ->comment('Tipo: GPU, CPU, RAM, SSD, Monitor, Periférico, Outro');

            // Status do monitoramento
            $table->boolean('ativo')
                  ->default(true)
                  ->after('preco_atual')
                  ->comment('Se false, o robô de preços ignora este componente');

            // Extremos de preço (atualizados automaticamente)
            $table->decimal('preco_minimo', 10, 2)
                  ->nullable()
                  ->after('ativo')
                  ->comment('Menor preço já registrado');

            $table->decimal('preco_maximo', 10, 2)
                  ->nullable()
                  ->after('preco_minimo')
                  ->comment('Maior preço já registrado');

            // Índices
            $table->index('categoria');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('componentes_hardware', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['categoria']);
            $table->dropIndex(['ativo']);
            $table->dropColumn(['user_id', 'categoria', 'ativo', 'preco_minimo', 'preco_maximo']);
        });
    }
};
