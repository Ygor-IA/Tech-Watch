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
        Schema::create('historicos_preco', function (Blueprint $table) {
            $table->id();
            // Chave estrangeira ligando ao componente
            $table->foreignId('componente_hardware_id')->constrained('componentes_hardware')->onDelete('cascade');
            $table->decimal('preco', 10, 2);
            $table->timestamp('registrado_em')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_precos');
    }
};
