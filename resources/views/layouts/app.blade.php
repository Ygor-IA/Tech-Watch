<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor de Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #0f1012;">

<nav class="navbar navbar-expand-lg shadow-sm" style="background-color: #111214; border-bottom: 2px solid #ff6500;">
    <div class="container">
        <a class="navbar-brand text-white fw-bold" href="{{ route('componentes.index') }}">
            TECH<span style="color: #ff6500;">-WATCH</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTech">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTech">
            <ul class="navbar-nav me-auto">
                </ul>
            
            <div class="d-flex align-items-center mt-2 mt-lg-0">
                @auth
                    <span style="color: #c0c6d4; font-size: 0.95rem;" class="me-3">
                        Olá, <strong class="text-white">{{ Auth::user()->name }}</strong>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm text-white fw-bold px-3" style="background-color: #ff6500; border: none; transition: 0.2s;">Sair</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm text-white fw-bold px-3" style="background-color: transparent; border: 1px solid #ff6500;">Entrar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<style>
    body { background-color: #0f1012; color: #ffffff; }
</style>
    <div class="container">
        @yield('conteudo')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>