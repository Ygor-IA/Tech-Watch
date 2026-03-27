@extends('layouts.app')

@section('conteudo')
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-lg" style="background-color: #1e2024; border-radius: 8px;">
            <div class="card-body p-5">
                <h3 class="text-center fw-bold text-white mb-4">Criar Nova Conta</h3>
                
                @if($errors->any())
                    <div class="alert alert-danger" style="background-color: #2b1414; color: #ff6c6c; border: 1px solid #ff4d4d;">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #c0c6d4;">Nome Completo</label>
                        <input type="text" name="name" class="form-control text-white" style="background-color: #2b2e35; border: 1px solid #4a4e58; padding: 10px;" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #c0c6d4;">E-mail</label>
                        <input type="email" name="email" class="form-control text-white" style="background-color: #2b2e35; border: 1px solid #4a4e58; padding: 10px;" required value="{{ old('email') }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold" style="color: #c0c6d4;">Senha</label>
                            <input type="password" name="password" class="form-control text-white" style="background-color: #2b2e35; border: 1px solid #4a4e58; padding: 10px;" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold" style="color: #c0c6d4;">Confirmar Senha</label>
                            <input type="password" name="password_confirmation" class="form-control text-white" style="background-color: #2b2e35; border: 1px solid #4a4e58; padding: 10px;" required>
                        </div>
                    </div>
                    <button type="submit" class="btn w-100 fw-bold text-white mb-4" style="background-color: #ff6500; border: none; padding: 12px; font-size: 1.1rem;">FINALIZAR CADASTRO</button>
                    
                    <div class="text-center border-top pt-3" style="border-color: #3d414a !important;">
                        <span style="color: #8c92a0;">Já possui uma conta?</span> 
                        <a href="{{ route('login') }}" class="fw-bold" style="color: #ff6500; text-decoration: none;">Faça Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection