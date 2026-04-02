@extends('layouts.app')

@section('titulo', 'Adicionar Hardware — Tech-Watch')

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
                    <h1 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin: 0;">
                        ＋ Adicionar Novo Hardware
                    </h1>
                    <p style="color: var(--tw-text-muted); font-size: 0.85rem; margin: 6px 0 0;">
                        Insira os dados do componente que você deseja monitorar.
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

                    <form action="{{ route('componentes.store') }}" method="POST">
                        @csrf

                        <div class="tw-mb-3">
                            <label class="tw-label">Nome do Componente</label>
                            <input type="text" name="nome" class="tw-input" required 
                                   value="{{ old('nome') }}" 
                                   placeholder="Ex: Monitor Gamer Samsung Odyssey 27'">
                        </div>

                        <div class="tw-mb-3">
                            <label class="tw-label">Link da Loja (URL)</label>
                            <input type="url" name="link" class="tw-input" required 
                                   value="{{ old('link') }}" 
                                   placeholder="Ex: https://www.mercadolivre.com.br/...">
                            <p class="tw-form-hint">Cole o link completo da página do produto. O nosso robô fará o resto.</p>
                        </div>

                        <hr style="border-color: rgba(0, 229, 255, 0.08); margin: 2rem 0;">

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="tw-btn tw-btn-primary tw-btn-lg">
                                ⚡ CADASTRAR HARDWARE
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection