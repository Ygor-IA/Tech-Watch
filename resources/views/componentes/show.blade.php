@extends('layouts.app')

@section('conteudo')
<div class="mt-4 mb-4">
    <a href="{{ route('componentes.index') }}" class="btn btn-sm text-white shadow-sm px-3" style="background-color: #3d414a; border: none; transition: 0.2s;">
        &larr; Voltar para a Lista
    </a>
</div>

@if(session('sucesso'))
    <div class="alert shadow-sm border-0 mb-4" style="background-color: #1a2e23; color: #4ade80; border-left: 4px solid #22c55e !important;">
        {{ session('sucesso') }}
    </div>
@endif

<div class="card shadow-lg mb-4 border-0 border-start border-5" style="background-color: #1e2024; border-left-color: #ff6500 !important; border-radius: 12px;">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 p-4">
        <div>
            <h2 class="mb-1 text-white fw-bold">{{ $componente->nome }}</h2>
            <a href="{{ $componente->link }}" target="_blank" style="color: #ff6500; text-decoration: none; font-weight: 500;">Acessar página na loja ↗</a>
        </div>
        <div class="text-md-end text-center p-3 rounded" style="background-color: #111214;">
            <span class="d-block text-uppercase small fw-bold" style="color: #8c92a0;">Preço Atual</span>
            <h2 class="mb-0 fw-bold" style="color: #4ade80;">{{ $componente->preco_formatado }}</h2>
        </div>
    </div>
</div>

<div class="card border-0 mb-4 shadow-lg" style="background-color: #1e2024; border-radius: 12px;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-3 mb-lg-0 text-center text-lg-start">
                <h5 class="text-white fw-bold mb-1">🔔 Alerta de Preço</h5>
                <p class="mb-0 small" style="color: #8c92a0;">Quer pagar menos? Avisamos você por e-mail assim que o preço cair.</p>
            </div>
            <div class="col-lg-7">
            <form action="{{ route('componentes.alerta', $componente->id) }}" method="POST" class="d-flex flex-column flex-md-row gap-3">
                    @csrf
                    
                    <div class="input-group shadow-sm" style="max-width: 250px;">
                        <span class="input-group-text text-white fw-bold" style="background-color: #3d414a; border: 1px solid #3d414a;">{{ $componente->simbolo_moeda }}</span>
                        <input type="number" step="0.01" name="preco_alvo" class="form-control text-white" 
                               style="background-color: #2b2e35; border: 1px solid #3d414a;" 
                               placeholder="Preço desejado" required>
                    </div>
                    
                    <button type="submit" class="btn text-white fw-bold px-4 shadow-sm" 
                            style="background-color: #ff6500; border: none; white-space: nowrap; transition: 0.2s;">
                        Criar Alerta
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-lg border-0" style="background-color: #1e2024; border-radius: 12px;">
    <div class="card-header border-bottom-0 pt-4 pb-0" style="background-color: transparent;">
        <h5 class="mb-0 fw-bold" style="color: #c0c6d4;">Histórico de Variação</h5>
    </div>
    <div class="card-body p-4">
        <canvas id="graficoPrecos" height="100"></canvas>
        
        @if($componente->historicosPreco->isEmpty())
            <div class="alert mt-4 text-center border-0" style="background-color: #2b2e35; color: #8c92a0;" role="alert">
                Aguardando a primeira verificação do robô para desenhar o gráfico.
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configura as cores padrão do Chart.js para o Dark Mode
    Chart.defaults.color = '#8c92a0'; // Cor das letras do gráfico
    Chart.defaults.borderColor = '#2b2e35'; // Cor das linhas de grade do fundo

    const historico = @json($componente->historicosPreco);
    const simboloMoeda = @json($componente->simbolo_moeda); 

    if (historico.length > 0) {
        const labels = historico.map(item => {
            let data = new Date(item.registrado_em);
            return data.toLocaleDateString('pt-BR') + ' ' + data.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
        });
        
        const dataPrices = historico.map(item => parseFloat(item.preco));

        const ctx = document.getElementById('graficoPrecos').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Variação de Preço (' + simboloMoeda + ')', 
                    data: dataPrices,
                    borderColor: '#ff6500',
                    backgroundColor: 'rgba(255, 101, 0, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#ff6500',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { 
                responsive: true,
                scales: {
                    x: {
                        grid: { color: '#2b2e35' }
                    },
                    y: {
                        grid: { color: '#2b2e35' },
                        beginAtZero: false,
                        ticks: {
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
                    tooltip: {
                        backgroundColor: '#111214',
                        titleColor: '#c0c6d4',
                        bodyColor: '#ffffff',
                        borderColor: '#ff6500',
                        borderWidth: 1,
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