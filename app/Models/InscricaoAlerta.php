<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscricaoAlerta extends Model
{
    protected $table = 'inscricoes_alerta';
    
    protected $fillable = ['componente_hardware_id', 'email_usuario', 'preco_alvo', 'notificado_em'];

    protected function casts(): array
    {
        return [
            'preco_alvo' => 'decimal:2',
            'notificado_em' => 'datetime',
        ];
    }

    // Relacionamento: Esta inscrição pertence a um componente específico
    public function componente()
    {
        return $this->belongsTo(ComponenteHardware::class, 'componente_hardware_id');
    }

    // Scope: Apenas alertas que ainda não foram notificados
    public function scopePendentes($query)
    {
        return $query->whereNull('notificado_em');
    }
}
