@extends('layouts.app')

@section('titulo', 'Editar: ' . $componente->nome . ' — Tech-Watch')

@section('conteudo')
<div class="tw-section">

    {{-- ===== BACK BUTTON ===== --}}
    <div class="tw-mb-3">
        <a href="{{ route('componentes.index') }}" class="tw-btn tw-btn-sm tw-btn-secondary">
            ← Voltar para a Lista
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="tw-card">
                {{-- Card Header --}}
                <div class="tw-card-header">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <span class="tw-badge tw-badge-orange">Editando</span>
                        <span class="tw-hw-card-id">#{{ str_pad($componente->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h1 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin: 6px 0 0;">
                        ✏️ Editar Hardware
                    </h1>
                    <p style="color: var(--tw-text-muted); font-size: 0.85rem; margin: 6px 0 0;">
                        Atualize os dados do componente <strong class="tw-text-cyan">{{ $componente->nome }}</strong>.
                    </p>
                </div>

                {{-- Card Body --}}
                <div class="tw-card-body">

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="tw-alert-error tw-mb-3">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('componentes.update', $componente->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="tw-mb-3">
                            <label class="tw-label">Nome do Componente</label>
                            <input type="text" name="nome" class="tw-input" required 
                                   value="{{ old('nome', $componente->nome) }}" 
                                   placeholder="Ex: Monitor Gamer Samsung Odyssey 27'">
                        </div>

                        <div class="tw-mb-3">
                            <label class="tw-label">Link da Loja (URL)</label>
                            <input type="url" name="link" class="tw-input" required 
                                   value="{{ old('link', $componente->link) }}" 
                                   placeholder="Ex: https://www.mercadolivre.com.br/...">
                            <p class="tw-form-hint">Cole o link completo da página do produto.</p>
                        </div>

                        <div class="tw-mb-3">
                            <label class="tw-label">Preço Atual (Opcional)</label>
                            <div class="tw-input-group" style="max-width: 280px;">
                                <span class="tw-input-prefix">{{ $componente->simbolo_moeda }}</span>
                                <input type="number" step="0.01" name="preco_atual" class="tw-input"
                                       value="{{ old('preco_atual', $componente->preco_atual) }}" 
                                       placeholder="0,00" style="border-radius: 0 10px 10px 0;">
                            </div>
                            <p class="tw-form-hint">O preço é atualizado automaticamente pelo robô. Preencha apenas se quiser alterar manualmente.</p>
                        </div>

                        <hr style="border-color: rgba(0, 229, 255, 0.08); margin: 2rem 0;">

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <a href="{{ route('componentes.index') }}" class="tw-btn tw-btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="tw-btn tw-btn-primary tw-btn-lg">
                                ✅ SALVAR ALTERAÇÕES
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
