@extends('layouts.app')

@section('conteudo')
<div class="mb-4">
    <a href="{{ route('componentes.index') }}" class="btn btn-outline-secondary">&larr; Voltar para a Lista</a>
</div>

@if(session('sucesso'))
    <div class="alert alert-success shadow-sm">
        {{ session('sucesso') }}
    </div>
@endif

<div class="card shadow-sm mb-4 border-0 border-start border-primary border-5">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div>
            <h2 class="mb-0 text-dark fw-bold">{{ $componente->nome }}</h2>
            <a href="{{ $componente->link }}" target="_blank" class="text-decoration-none text-primary">Acessar página na loja</a>
        </div>
        <div class="text-md-end text-center bg-light p-3 rounded">
            <span class="text-muted d-block text-uppercase small fw-bold">Preço Atual</span>
            <h2 class="text-success mb-0 fw-bold">R$ {{ number_format($componente->preco_atual, 2, ',', '.') }}</h2>
        </div>
    </div>
</div>

<div class="card border-0 mb-4" style="background-color: #1e2024; border-radius: 8px; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-3 mb-lg-0 text-center text-lg-start">
                <h5 class="text-white fw-bold mb-1">🔔 Alerta de Preço</h5>
                <p class="mb-0 small" style="color: #a0a5b1;">Quer pagar menos? Avisamos você por e-mail assim que o preço cair.</p>
            </div>
            <div class="col-lg-7">
                <form action="{{ route('componentes.alerta', $componente->id) }}" method="POST" class="d-flex flex-column flex-md-row gap-2">
                    @csrf
                    
                    <input type="email" name="email_usuario" class="form-control text-white" 
                           style="background-color: #2b2e35; border: 1px solid #3d414a;" 
                           placeholder="Seu melhor e-mail" required>
                    
                    <div class="input-group" style="min-width: 180px;">
                        <span class="input-group-text text-white" style="background-color: #3d414a; border: 1px solid #3d414a;">R$</span>
                        <input type="number" step="0.01" name="preco_alvo" class="form-control text-white" 
                               style="background-color: #2b2e35; border: 1px solid #3d414a;" 
                               placeholder="Preço desejado" required>
                    </div>
                    
                    <button type="submit" class="btn text-white fw-bold px-4" 
                            style="background-color: #ff6500; border: none; white-space: nowrap; transition: 0.2s;">
                        Criar Alerta
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="mb-0 text-secondary">Histórico de Variação</h5>
    </div>
    <div class="card-body">
        <canvas id="graficoPrecos" height="100"></canvas>
        
        @if($componente->historicosPreco->isEmpty())
            <div class="alert alert-light border mt-3 text-center" role="alert">
                Aguardando a primeira verificação do robô para desenhar o gráfico.
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const historico = @json($componente->historicosPreco);

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
                    label: 'Variação de Preço (R$)',
                    data: dataPrices,
                    borderColor: '#ff6500', // Linha do gráfico acompanhando a cor do botão
                    backgroundColor: 'rgba(255, 101, 0, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#ff6500',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true }
        });
    }
</script>
@endsection