<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComponenteHardware extends Model
{
    // Forçando o nome da tabela em português para o Laravel não se perder
    protected $table = 'componentes_hardware'; 
    
    protected $fillable = ['nome', 'link', 'preco_atual'];

    // Relacionamento: Um componente tem vários históricos de preço
    public function historicosPreco()
    {
        return $this->hasMany(HistoricoPreco::class, 'componente_hardware_id');
    }

    // Relacionamento: Um componente tem várias inscrições de alerta
    public function inscricoesAlerta()
    {
        return $this->hasMany(InscricaoAlerta::class, 'componente_hardware_id');
    }
}
