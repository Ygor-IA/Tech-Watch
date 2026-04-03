@extends('layouts.app')

@section('titulo', 'Tech-Watch — Dashboard')

@section('conteudo')
<div class="tw-section">

    {{-- ===== HEADER ===== --}}
    <div class="tw-section-header">
        <div>
            <h1 class="tw-section-title">
                <span class="title-icon">📡</span>
                Hardwares Monitorados
            </h1>
            <p class="tw-section-subtitle">Monitoramento em tempo real dos preços dos seus componentes</p>
        </div>
        <a href="{{ route('componentes.create') }}" class="tw-btn tw-btn-primary tw-btn-lg">
            ＋ Novo Hardware
        </a>
    </div>

    {{-- ===== STATS ===== --}}
    <div class="tw-stats">
        <div class="tw-stat-card">
            <div class="tw-stat-value">{{ $componentes->count() }}</div>
            <div class="tw-stat-label">Componentes</div>
        </div>
        <div class="tw-stat-card">
            <div class="tw-stat-value">24/7</div>
            <div class="tw-stat-label">Monitoramento</div>
        </div>
        <div class="tw-stat-card">
            <div class="tw-stat-value">⚡</div>
            <div class="tw-stat-label">Alertas Ativos</div>
        </div>
    </div>

    {{-- ===== SUCCESS MESSAGE ===== --}}
    @if(session('sucesso'))
        <div class="tw-alert-success tw-mb-3">
            ✅ {{ session('sucesso') }}
        </div>
    @endif

    {{-- ===== HARDWARE GRID ===== --}}
    @if($componentes->count() > 0)
        <div class="tw-hw-grid">
            @foreach($componentes as $componente)
                <div class="tw-hw-card">
                    <div class="tw-hw-card-top" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
                        <span class="tw-hw-card-id" style="margin-right: auto;">#{{ str_pad($componente->id, 3, '0', STR_PAD_LEFT) }}</span>
                        <span class="tw-badge tw-badge-secondary">{{ $componente->categoria_label }}</span>
                        <span class="tw-badge tw-badge-cyan">{{ $componente->simbolo_moeda }}</span>
                        @if(!$componente->ativo)
                            <span class="tw-badge tw-badge-orange" style="background: rgba(255, 107, 0, 0.1); color: #ff6b00; border: 1px solid rgba(255, 107, 0, 0.2);">⏸️ Pausado</span>
                        @endif
                    </div>

                    <h3 class="tw-hw-card-name">{{ $componente->nome }}</h3>
                    
                    <div class="tw-hw-card-price">{{ $componente->preco_formatado }}</div>

                    <a href="{{ $componente->link }}" target="_blank" class="tw-hw-card-link">
                        Acessar Loja →
                    </a>

                    <div class="tw-hw-card-actions">
                        <a href="{{ route('componentes.show', $componente->id) }}" class="tw-btn tw-btn-sm tw-btn-cyan" style="flex: 1; justify-content: center;">
                            📊 Gráfico
                        </a>
                        <a href="{{ route('componentes.edit', $componente->id) }}" class="tw-btn tw-btn-sm tw-btn-secondary" style="flex: 1; justify-content: center;">
                            ✏️ Editar
                        </a>
                        <form action="{{ route('componentes.destroy', $componente->id) }}" method="POST" class="d-inline" style="flex: 1;" onsubmit="return confirm('Tem certeza que deseja remover este item?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="tw-btn tw-btn-sm tw-btn-danger" style="width: 100%; justify-content: center;">
                                🗑️ Excluir
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="tw-card">
            <div class="tw-empty">
                <div class="tw-empty-icon">📦</div>
                <p class="tw-empty-text">
                    Nenhum componente cadastrado ainda.<br>
                    Clique em <strong class="tw-text-orange">＋ Novo Hardware</strong> para começar a monitorar!
                </p>
            </div>
        </div>
    @endif
</div>
@endsection