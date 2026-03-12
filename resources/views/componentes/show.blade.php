@extends('layouts.app')

@section('conteudo')
<div class="mb-4">
    <a href="{{ route('componentes.index') }}" class="btn btn-secondary">&larr; Voltar para a Lista</a>
</div>

<div class="card shadow-sm mb-4 border-0 border-start border-primary border-5">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0 text-dark fw-bold">{{ $componente->nome }}</h2>
            <a href="{{ $componente->link }}" target="_blank" class="text-decoration-none text-primary">Acessar link da loja</a>
        </div>
        <div class="text-end">
            <span class="text-muted d-block text-uppercase small fw-bold">Preço Atual</span>
            <h3 class="text-success mb-0 fw-bold">R$ {{ number_format($componente->preco_atual, 2, ',', '.') }}</h3>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="mb-0 text-secondary">Histórico de Preços</h5>
    </div>
    <div class="card-body">
        <canvas id="graficoPrecos" height="100"></canvas>
        
        @if($componente->historicosPreco->isEmpty())
            <div class="alert alert-warning mt-3 text-center" role="alert">
                Ainda não há dados de histórico de preços para este componente. Insira alguns dados no banco para visualizar o gráfico!
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Pegando os dados do histórico enviados pelo Laravel e convertendo para JavaScript
    const historico = @json($componente->historicosPreco);

    // Se houver dados, montamos o gráfico
    if (historico.length > 0) {
        // Preparando os rótulos (Eixo X - Datas)
        const labels = historico.map(item => {
            let data = new Date(item.registrado_em);
            return data.toLocaleDateString('pt-BR') + ' ' + data.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
        });
        
        // Preparando os valores (Eixo Y - Preços)
        const dataPrices = historico.map(item => parseFloat(item.preco));

        // Configuração e renderização do gráfico de linha
        const ctx = document.getElementById('graficoPrecos').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Variação de Preço (R$)',
                    data: dataPrices,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3 // Deixa a linha mais curva e suave
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toFixed(2).replace('.', ',');
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection