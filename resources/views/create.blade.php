@extends('layouts.app')

@section('conteudo')
<div class="card shadow-sm max-w-md mx-auto" style="max-width: 600px;">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Adicionar Novo Hardware</h4>
    </div>
    <div class="card-body">
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('componentes.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Componente (ex: RTX 4060)</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" required>
            </div>

            <div class="mb-3">
                <label for="link" class="form-label">Link da Loja</label>
                <input type="url" class="form-control" id="link" name="link" value="{{ old('link') }}" required placeholder="https://...">
            </div>

            <div class="mb-4">
                <label for="preco_atual" class="form-label">Preço Atual (Opcional)</label>
                <input type="number" step="0.01" class="form-control" id="preco_atual" name="preco_atual" value="{{ old('preco_atual') }}">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Salvar Componente</button>
                <a href="{{ route('componentes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection