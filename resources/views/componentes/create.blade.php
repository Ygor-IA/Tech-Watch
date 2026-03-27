@extends('layouts.app')

@section('conteudo')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        
        <div class="mb-3">
            <a href="{{ route('componentes.index') }}" class="btn btn-sm text-white shadow-sm px-3" style="background-color: #3d414a; border: none;">
                &larr; Voltar para a Lista
            </a>
        </div>

        <div class="card border-0 shadow-lg" style="background-color: #1e2024; border-radius: 12px;">
            <div class="card-header border-0 pt-4 pb-0" style="background-color: transparent;">
                <h3 class="fw-bold text-white mb-0">Adicionar Novo Hardware</h3>
                <p style="color: #8c92a0; font-size: 0.9rem;">Insira os dados do componente que você deseja monitorar.</p>
            </div>
            
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger" style="background-color: #2b1414; color: #ff6c6c; border: 1px solid #ff4d4d; border-radius: 8px;">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('componentes.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color: #c0c6d4;">Nome do Componente</label>
                        <input type="text" name="nome" class="form-control text-white shadow-sm" style="background-color: #2b2e35; border: 1px solid #4a4e58; padding: 12px;" required value="{{ old('nome') }}" placeholder="Ex: Monitor Gamer Samsung Odyssey 27'">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color: #c0c6d4;">Link da Loja (URL)</label>
                        <input type="url" name="link" class="form-control text-white shadow-sm" style="background-color: #2b2e35; border: 1px solid #4a4e58; padding: 12px;" required value="{{ old('link') }}" placeholder="Ex: https://www.mercadolivre.com.br/...">
                        <div class="form-text" style="color: #8c92a0; font-size: 0.85rem;">Cole o link completo da página do produto. O nosso robô fará o resto.</div>
                    </div>
                    
                    <hr style="border-color: #3d414a; margin: 30px 0;">

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn fw-bold text-white shadow-sm px-5 py-2" style="background-color: #ff6500; border: none; font-size: 1.1rem; transition: 0.2s;">
                            + CADASTRAR HARDWARE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection