<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoPreco extends Model
{
    protected $table = 'historicos_preco';
    
    protected $fillable = ['componente_hardware_id', 'preco', 'registrado_em'];

    // Relacionamento: Este histórico pertence a um componente específico
    public function componente()
    {
        return $this->belongsTo(ComponenteHardware::class, 'componente_hardware_id');
    }
}
