@extends('layouts.app')

@section('conteudo')
<div class="row mb-4 mt-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold mb-0" style="color: #c0c6d4;">Hardwares Monitorados</h2>
    </div>
    <div class="col-md-4 text-end mt-3 mt-md-0">
        <a href="{{ route('componentes.create') }}" class="btn fw-bold text-white shadow-sm px-4 py-2" style="background-color: #ff6500; border: none; transition: 0.2s;">
            + Adicionar Novo Hardware
        </a>
    </div>
</div>

@if(session('sucesso'))
    <div class="alert shadow-sm border-0 mb-4" style="background-color: #1a2e23; color: #4ade80; border-left: 4px solid #22c55e !important;">
        {{ session('sucesso') }}
    </div>
@endif

<div class="card border-0 shadow-lg" style="background-color: #1e2024; border-radius: 12px; overflow: hidden;">
    <div class="card-body p-0"> <div class="table-responsive">
            <table class="table align-middle mb-0" style="color: #c0c6d4;">
                <thead style="background-color: #111214; color: #ffffff;">
                    <tr>
                        <th class="border-0 px-4 py-3 text-muted">ID</th>
                        <th class="border-0 py-3">Nome do Componente</th>
                        <th class="border-0 py-3">Preço Atual</th>
                        <th class="border-0 py-3">Link da Loja</th>
                        <th class="text-center border-0 px-4 py-3">Ações</th>
                    </tr>
                </thead>
                <tbody style="border-top: none;">
                    @forelse($componentes as $componente)
                    <tr style="border-bottom: 1px solid #2b2e35;">
                        <td class="px-4 py-3" style="background-color: transparent; color: #8c92a0;">{{ $componente->id }}</td>
                        <td class="py-3" style="background-color: transparent;"><strong class="text-white">{{ $componente->nome }}</strong></td>
                        <td class="py-3 fw-bold" style="background-color: transparent; color: #4ade80;">{{ $componente->preco_formatado }}</td>
                        <td class="py-3" style="background-color: transparent;">
                            <a href="{{ $componente->link }}" target="_blank" style="color: #ff6500; text-decoration: none; font-weight: 500;">Acessar Loja ↗</a>
                        </td>
                        <td class="text-center px-4 py-3" style="background-color: transparent;">
                            <a href="{{ route('componentes.show', $componente->id) }}" class="btn btn-sm text-white fw-bold me-2 px-3" style="background-color: #3d414a; border: none;">Gráfico</a>
                            
                            <form action="{{ route('componentes.destroy', $componente->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-white fw-bold px-3" style="background-color: #dc3545; border: none;">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="background-color: transparent; color: #8c92a0;">
                            <div class="mb-2" style="font-size: 2rem;">📦</div>
                            Nenhum componente cadastrado ainda. <br> Clique no botão laranja acima para começar a monitorar!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection