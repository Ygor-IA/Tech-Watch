<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponenteHardware extends Model
{
    use SoftDeletes;

    // Forçando o nome da tabela em português para o Laravel não se perder
    protected $table = 'componentes_hardware'; 
    
    protected $fillable = [
        'user_id',
        'nome',
        'categoria',
        'link',
        'preco_atual',
        'ativo',
        'preco_minimo',
        'preco_maximo',
    ];

    protected function casts(): array
    {
        return [
            'preco_atual'  => 'decimal:2',
            'preco_minimo' => 'decimal:2',
            'preco_maximo' => 'decimal:2',
            'ativo'        => 'boolean',
        ];
    }

    // ─── CONSTANTES DE CATEGORIA ────────────────────────────────────
    const CATEGORIAS = [
        'GPU'        => '🎮 Placa de Vídeo',
        'CPU'        => '⚙️ Processador',
        'RAM'        => '🧠 Memória RAM',
        'SSD'        => '💾 Armazenamento',
        'Monitor'    => '🖥️ Monitor',
        'Periferico' => '🖱️ Periférico',
        'Placa-Mae'  => '🔌 Placa-Mãe',
        'Fonte'      => '⚡ Fonte',
        'Gabinete'   => '🏗️ Gabinete',
        'Outro'      => '📦 Outro',
    ];

    // ─── RELACIONAMENTOS ────────────────────────────────────────────

    // Um componente pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Um componente tem vários históricos de preço
    public function historicosPreco()
    {
        return $this->hasMany(HistoricoPreco::class, 'componente_hardware_id');
    }

    // Um componente tem várias inscrições de alerta
    public function inscricoesAlerta()
    {
        return $this->hasMany(InscricaoAlerta::class, 'componente_hardware_id');
    }

    // ─── SCOPES ─────────────────────────────────────────────────────

    // Scope: apenas componentes ativos (para o robô de preços)
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Scope: filtrar por categoria
    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Scope: apenas componentes de um usuário específico
    public function scopeDoUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── ACESSORES ──────────────────────────────────────────────────

    protected $appends = ['simbolo_moeda', 'preco_formatado', 'categoria_label'];

    // Descobre a moeda baseada no link
    public function getSimboloMoedaAttribute()
    {
        return str_contains($this->link, 'webscraper.io') ? 'US$' : 'R$';
    }

    // Formata o preço de acordo com a moeda
    public function getPrecoFormatadoAttribute()
    {
        if ($this->preco_atual === null) {
            return 'Aguardando...';
        }

        if ($this->simbolo_moeda === 'US$') {
            return 'US$ ' . number_format((float)$this->preco_atual, 2, '.', ',');
        }
        
        return 'R$ ' . number_format((float)$this->preco_atual, 2, ',', '.');
    }

    // Retorna o label amigável da categoria
    public function getCategoriaLabelAttribute()
    {
        return self::CATEGORIAS[$this->categoria] ?? '📦 Outro';
    }

    // ─── MÉTODOS AUXILIARES ─────────────────────────────────────────

    /**
     * Atualiza os extremos de preço (mínimo/máximo)
     * Chamado pelo robô após raspar um novo preço.
     */
    public function atualizarExtremos(float $novoPreco): void
    {
        $changes = [];

        if ($this->preco_minimo === null || $novoPreco < (float) $this->preco_minimo) {
            $changes['preco_minimo'] = $novoPreco;
        }

        if ($this->preco_maximo === null || $novoPreco > (float) $this->preco_maximo) {
            $changes['preco_maximo'] = $novoPreco;
        }

        if (!empty($changes)) {
            $this->update($changes);
        }
    }
}
