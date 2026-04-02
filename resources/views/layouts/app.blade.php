<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Tech-Watch — Monitore preços de hardware em tempo real. Receba alertas quando o preço cair.">
    <meta name="keywords" content="hardware, monitoramento de preços, tech, placa de vídeo, processador, alertas">
    <meta name="author" content="Tech-Watch">
    
    <title>@yield('titulo', 'Tech-Watch — Monitoramento de Hardware')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="tw-navbar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="{{ route('componentes.index') }}">
                <span style="color: #fff;">TECH</span><span class="brand-accent">-WATCH</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                @auth
                    <span class="nav-user d-none d-sm-inline">
                        Olá, <strong>{{ Auth::user()->name }}</strong>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="tw-btn tw-btn-sm tw-btn-secondary">
                            ⏻ Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="tw-btn tw-btn-sm tw-btn-cyan">
                        ⚡ Entrar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ===== MAIN CONTENT ===== --}}
<main class="container" style="padding-top: 2rem; padding-bottom: 2rem; position: relative; z-index: 1;">
    @yield('conteudo')
</main>

{{-- ===== FOOTER ===== --}}
<footer class="tw-footer">
    <div class="container">
        <div class="tw-footer-content">
            <div class="tw-footer-brand">
                TECH<span class="brand-accent">-WATCH</span>
            </div>
            <div class="tw-footer-text">
                © {{ date('Y') }} Tech-Watch — Monitoramento inteligente de preços de hardware.
            </div>
        </div>
    </div>
</footer>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>