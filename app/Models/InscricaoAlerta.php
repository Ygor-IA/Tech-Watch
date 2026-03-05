<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscricaoAlerta extends Model
{
    protected $table = 'inscricoes_alerta';
    
    protected $fillable = ['componente_hardware_id', 'email_usuario', 'preco_alvo'];

    // Relacionamento: Esta inscrição pertence a um componente específico
    public function componente()
    {
        return $this->belongsTo(ComponenteHardware::class, 'componente_hardware_id');
    }
}
