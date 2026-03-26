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
    // 1. Diz ao Laravel para enviar esses novos campos quando formos usar o Chart.js
    protected $appends = ['simbolo_moeda', 'preco_formatado'];

    // 2. Acessor: Descobre a moeda baseada no link
    public function getSimboloMoedaAttribute()
    {
        return str_contains($this->link, 'webscraper.io') ? 'US$' : 'R$';
    }

    // 3. Acessor: Formata o preço perfeitamente de acordo com a moeda
    public function getPrecoFormatadoAttribute()
    {
        if ($this->simbolo_moeda === 'US$') {
            return 'US$ ' . number_format($this->preco_atual, 2, '.', ',');
        }
        
        return 'R$ ' . number_format($this->preco_atual, 2, ',', '.');
    }
}
