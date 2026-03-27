@extends('layouts.app')

@section('conteudo')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card border-0 shadow-lg" style="background-color: #1e2024; border-radius: 8px;">
            <div class="card-body p-5">
                <h3 class="text-center fw-bold text-white mb-4">Acessar Conta</h3>
                
                @if($errors->any())
                    <div class="alert alert-danger" style="background-color: #2b1414; color: #ff6c6c; border: 1px solid #ff4d4d;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #c0c6d4;">E-mail</label>
                        <input type="email" name="email" class="form-control text-white" style="background-color: #2b2e35; border: 1px solid #4a4e58; padding: 10px;" required value="{{ old('email') }}" placeholder="Digite seu e-mail">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color: #c0c6d4;">Senha</label>
                        <input type="password" name="password" class="form-control text-white" style="background-color: #2b2e35; border: 1px solid #4a4e58; padding: 10px;" required placeholder="Sua senha secreta">
                    </div>
                    <button type="submit" class="btn w-100 fw-bold text-white mb-4" style="background-color: #ff6500; border: none; padding: 12px; font-size: 1.1rem;">ENTRAR</button>
                    
                    <div class="text-center border-top pt-3" style="border-color: #3d414a !important;">
                        <span style="color: #8c92a0;">Ainda não tem conta?</span> 
                        <a href="{{ route('register') }}" class="fw-bold" style="color: #ff6500; text-decoration: none;">Cadastre-se aqui</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection