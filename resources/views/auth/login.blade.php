@extends('layouts.app')

@section('titulo', 'Login — Tech-Watch')

@section('conteudo')
<div class="tw-auth-wrapper">
    <div class="tw-auth-card">
        
        {{-- Logo --}}
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="font-size: 2rem; font-weight: 900; letter-spacing: 3px;">
                TECH<span style="background: linear-gradient(135deg, #00e5ff, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">-WATCH</span>
            </div>
        </div>

        <h2 class="tw-auth-title">Acessar Conta</h2>
        <p class="tw-auth-subtitle">Entre com suas credenciais para monitorar seus hardwares</p>

        {{-- Errors --}}
        @if($errors->any())
            <div class="tw-alert-error tw-mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="tw-mb-3">
                <label class="tw-label">E-mail</label>
                <input type="email" name="email" class="tw-input" required 
                       value="{{ old('email') }}" placeholder="seu@email.com">
            </div>

            <div class="tw-mb-4">
                <label class="tw-label">Senha</label>
                <input type="password" name="password" class="tw-input" required 
                       placeholder="Sua senha secreta">
            </div>

            <button type="submit" class="tw-btn tw-btn-primary tw-btn-lg" style="width: 100%; justify-content: center;">
                ⚡ ENTRAR
            </button>

            <div class="tw-auth-footer">
                <span>Ainda não tem conta?</span>
                <a href="{{ route('register') }}">Cadastre-se aqui</a>
            </div>
        </form>
    </div>
</div>
@endsection