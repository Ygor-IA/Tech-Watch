@extends('layouts.app')

@section('titulo', $componente->nome . ' — Tech-Watch')

@section('conteudo')
<div class="tw-section">

    {{-- ===== BACK BUTTON ===== --}}
    <div class="tw-mb-3">
        <a href="{{ route('componentes.index') }}" class="tw-btn tw-btn-sm tw-btn-secondary">
            ← Voltar para a Lista
        </a>
    </div>

    {{-- ===== SUCCESS MESSAGE ===== --}}
    @if(session('sucesso'))
        <div class="tw-alert-success tw-mb-3">
            ✅ {{ session('sucesso') }}
        </div>
    @endif

    {{-- ===== DETAIL HEADER CARD ===== --}}
    <div class="tw-card tw-card-featured tw-mb-3">
        <div class="tw-card-body">
            <div class="tw-detail-header">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <span class="tw-badge tw-badge-cyan">{{ $componente->simbolo_moeda }}</span>
                        <span class="tw-hw-card-id">#{{ str_pad($componente->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h1 style="font-size: 1.6rem; font-weight: 800; color: #fff; margin-bottom: 8px;">{{ $componente->nome }}</h1>
                    <a href="{{ $componente->link }}" target="_blank" class="tw-hw-card-link">
                        Acessar página na loja →
                    </a>
                </div>
                <div class="tw-detail-price-box">
                    <span class="tw-detail-price-label">Preço Atual</span>
                    <div class="tw-price">{{ $componente->preco_formatado }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PRICE ALERT BOX ===== --}}
    <div class="tw-alert-box tw-mb-3">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-3 mb-lg-0">
                <h5 style="color: #fff; font-weight: 700; margin-bottom: 4px; font-size: 1.1rem;">
                    🔔 Alerta de Preço
                </h5>
                <p style="color: var(--tw-text-muted); font-size: 0.85rem; margin-bottom: 0;">
                    Quer pagar menos? Avisamos você por e-mail assim que o preço cair.
                </p>
            </div>
            <div class="col-lg-7">
                <form action="{{ route('componentes.alerta', $componente->id) }}" method="POST" class="d-flex flex-column flex-md-row gap-3 align-items-start align-items-md-center">
                    @csrf
                    <div class="tw-input-group" style="max-width: 250px; flex-shrink: 0;">
                        <span class="tw-input-prefix">{{ $componente->simbolo_moeda }}</span>
                        <input type="number" step="0.01" name="preco_alvo" class="tw-input"
                               placeholder="Preço desejado" required style="border-radius: 0 10px 10px 0;">
                    </div>
                    <button type="submit" class="tw-btn tw-btn-primary" style="white-space: nowrap;">
                        ⚡ Criar Alerta
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== CHART CARD ===== --}}
    <div class="tw-card">
        <div class="tw-card-header">
            <h5 style="color: #fff; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;">
                <span class="title-icon" style="width: 30px; height: 30px; font-size: 0.9rem;">📈</span>
                Histórico de Variação
            </h5>
        </div>
        <div class="tw-card-body">
            <div class="tw-chart-container">
                <canvas id="graficoPrecos" height="100"></canvas>
            </div>

            @if($componente->historicosPreco->isEmpty())
                <div style="background: rgba(30, 41, 72, 0.5); border: 1px solid var(--tw-border-subtle); border-radius: 10px; padding: 1.5rem; text-align: center; margin-top: 1rem;">
                    <span style="font-size: 1.5rem;">⏳</span>
                    <p style="color: var(--tw-text-muted); margin: 0.5rem 0 0;">
                        Aguardando a primeira verificação do robô para desenhar o gráfico.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== CHART.JS ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = 'rgba(0, 229, 255, 0.06)';

    const historico = @json($componente->historicosPreco);
    const simboloMoeda = @json($componente->simbolo_moeda);

    if (historico.length > 0) {
        const labels = historico.map(item => {
            let data = new Date(item.registrado_em);
            return data.toLocaleDateString('pt-BR') + ' ' + data.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
        });

        const dataPrices = historico.map(item => parseFloat(item.preco));

        // Create gradient
        const ctx = document.getElementById('graficoPrecos').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height);
        gradient.addColorStop(0, 'rgba(0, 229, 255, 0.2)');
        gradient.addColorStop(0.5, 'rgba(139, 92, 246, 0.08)');
        gradient.addColorStop(1, 'rgba(0, 229, 255, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Variação de Preço (' + simboloMoeda + ')',
                    data: dataPrices,
                    borderColor: '#00e5ff',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#00e5ff',
                    pointBorderColor: '#0a0e1a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#00e5ff',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0, 229, 255, 0.04)' },
                        ticks: { font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(0, 229, 255, 0.04)' },
                        beginAtZero: false,
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            callback: function(value) {
                                if (simboloMoeda === 'US$') {
                                    return simboloMoeda + ' ' + value.toLocaleString('en-US', { minimumFractionDigits: 2 });
                                }
                                return simboloMoeda + ' ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: { family: 'Inter', weight: '600' },
                            color: '#94a3b8',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(10, 14, 26, 0.95)',
                        titleColor: '#94a3b8',
                        bodyColor: '#e2e8f0',
                        borderColor: '#00e5ff',
                        borderWidth: 1,
                        padding: 12,
                        titleFont: { family: 'Inter' },
                        bodyFont: { family: 'Inter', weight: '700' },
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                let valor = context.parsed.y;
                                if (simboloMoeda === 'US$') {
                                    return ' Preço: ' + simboloMoeda + ' ' + valor.toLocaleString('en-US', { minimumFractionDigits: 2 });
                                }
                                return ' Preço: ' + simboloMoeda + ' ' + valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection