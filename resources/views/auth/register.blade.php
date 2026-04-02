@extends('layouts.app')

@section('titulo', 'Cadastro — Tech-Watch')

@section('conteudo')
<div class="tw-auth-wrapper">
    <div class="tw-auth-card" style="max-width: 520px;">

        {{-- Logo --}}
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="font-size: 2rem; font-weight: 900; letter-spacing: 3px;">
                TECH<span style="background: linear-gradient(135deg, #00e5ff, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">-WATCH</span>
            </div>
        </div>

        <h2 class="tw-auth-title">Criar Nova Conta</h2>
        <p class="tw-auth-subtitle">Cadastre-se para monitorar preços e receber alertas</p>

        {{-- Errors --}}
        @if($errors->any())
            <div class="tw-alert-error tw-mb-3">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <div class="tw-mb-3">
                <label class="tw-label">Nome Completo</label>
                <input type="text" name="name" class="tw-input" required 
                       value="{{ old('name') }}" placeholder="Seu nome completo">
            </div>

            <div class="tw-mb-3">
                <label class="tw-label">E-mail</label>
                <input type="email" name="email" class="tw-input" required 
                       value="{{ old('email') }}" placeholder="seu@email.com">
            </div>

            <div class="row">
                <div class="col-md-6 tw-mb-3">
                    <label class="tw-label">Senha</label>
                    <input type="password" name="password" class="tw-input" required 
                           placeholder="Mínimo 8 caracteres">
                </div>
                <div class="col-md-6 tw-mb-3">
                    <label class="tw-label">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" class="tw-input" required 
                           placeholder="Repita a senha">
                </div>
            </div>

            <button type="submit" class="tw-btn tw-btn-primary tw-btn-lg" style="width: 100%; justify-content: center;">
                🚀 FINALIZAR CADASTRO
            </button>

            <div class="tw-auth-footer">
                <span>Já possui uma conta?</span>
                <a href="{{ route('login') }}">Faça Login</a>
            </div>
        </form>
    </div>
</div>
@endsection