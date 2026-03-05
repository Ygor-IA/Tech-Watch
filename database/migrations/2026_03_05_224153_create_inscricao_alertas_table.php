<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inscricoes_alerta', function (Blueprint $table) {
            $table->id();
            // Chave estrangeira ligando ao componente
            $table->foreignId('componente_hardware_id')->constrained('componentes_hardware')->onDelete('cascade');
            $table->string('email_usuario');
            $table->decimal('preco_alvo', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscricao_alertas');
    }
};
