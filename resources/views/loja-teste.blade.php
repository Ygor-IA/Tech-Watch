<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja Simulação — Tech Store</title>

    {{-- Meta tags para scraping (Schema.org) --}}
    <meta itemprop="price" content="1599.00">
    <meta itemprop="priceCurrency" content="BRL">

    {{-- JSON-LD para scraping avançado --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "Placa de Vídeo RTX 4060",
        "offers": {
            "@type": "Offer",
            "price": "1599.00",
            "priceCurrency": "BRL",
            "availability": "https://schema.org/InStock"
        }
    }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: #0a0e1a;
            font-family: 'Inter', sans-serif;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .store-card {
            background: rgba(12, 17, 32, 0.9);
            border: 1px solid rgba(0, 229, 255, 0.15);
            border-radius: 16px;
            padding: 2rem;
            max-width: 420px;
            width: 100%;
            backdrop-filter: blur(12px);
        }
        .store-card h3 { font-weight: 800; }
        .store-badge {
            display: inline-block;
            background: rgba(0, 229, 255, 0.1);
            color: #00e5ff;
            border: 1px solid rgba(0, 229, 255, 0.2);
            border-radius: 6px;
            padding: 2px 10px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .price_vista {
            font-size: 2rem;
            font-weight: 800;
            color: #4ade80;
            text-shadow: 0 0 15px rgba(74, 222, 128, 0.3);
        }
        .old-price {
            font-size: 1rem;
            color: #64748b;
            text-decoration: line-through;
        }
    </style>
</head>
<body>
    <div class="store-card text-center">
        <span class="store-badge mb-3">🏪 Loja Simulação</span>
        <h3 class="mt-3">Placa de Vídeo RTX 4060</h3>
        <p style="color: #64748b; font-size: 0.9rem;">Vendido e entregue por Loja Teste</p>
        <hr style="border-color: rgba(0, 229, 255, 0.1);">
        <p class="mb-1" style="color: #94a3b8; font-size: 0.85rem;">Preço com desconto no PIX:</p>
        <p class="old-price mb-1">De R$ 2.199,00</p>
        <h2 class="price_vista">R$ 1.599,00</h2>
        <p style="color: #64748b; font-size: 0.8rem; margin-top: 0.5rem;">ou 12x de R$ 149,91</p>
    </div>
</body>
</html>