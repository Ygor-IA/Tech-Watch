@extends('layouts.app')

@section('conteudo')
<div class="row mb-3">
    <div class="col-md-8">
        <h2>Hardwares Monitorados</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('componentes.create') }}" class="btn btn-success">Adicionar Novo Hardware</a>
    </div>
</div>

@if(session('sucesso'))
    <div class="alert alert-success">
        {{ session('sucesso') }}
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome do Componente</th>
                    <th>Preço Atual</th>
                    <th>Link da Loja</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($componentes as $componente)
                <tr>
                    <td>{{ $componente->id }}</td>
                    <td><strong>{{ $componente->nome }}</strong></td>
                    <td>R$ {{ number_format($componente->preco_atual, 2, ',', '.') }}</td>
                    <td><a href="{{ $componente->link }}" target="_blank" class="text-decoration-none">Acessar Loja</a></td>
                    <td class="text-center">
                        <a href="{{ route('componentes.show', $componente->id) }}" class="btn btn-sm btn-info text-white">Ver Gráfico</a>
                        
                        <form action="{{ route('componentes.destroy', $componente->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este item?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Nenhum componente cadastrado ainda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection