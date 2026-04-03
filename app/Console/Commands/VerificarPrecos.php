<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ComponenteHardware;
use App\Models\HistoricoPreco;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Notifications\AlertaPrecoBaixo;
use Illuminate\Support\Facades\Notification;

class VerificarPrecos extends Command
{
    protected $signature = 'precos:verificar';

    protected $description = 'Entra no link de cada hardware, raspa o preço atual e salva no histórico.';

    // Resultados para a tabela de resumo
    private array $resultados = [];

    public function handle()
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   🤖 TECH-WATCH — Verificador de Preços  ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->info('');

        $componentes = ComponenteHardware::ativos()->get();

        if ($componentes->isEmpty()) {
            $this->warn('Nenhum componente cadastrado para verificar.');
            return;
        }

        $this->info("📋 {$componentes->count()} componente(s) para verificar...");
        $this->info('');

        foreach ($componentes as $componente) {
            $this->verificarComponente($componente);
        }

        // Tabela de resumo no final
        $this->exibirResumo();

        $this->info('');
        $this->info('✅ Verificação concluída!');
    }

    /**
     * Verifica o preço de um componente individual
     */
    private function verificarComponente(ComponenteHardware $componente): void
    {
        $this->info("🔍 Verificando: {$componente->nome}");

        try {
            $url = $componente->link;

            // =============================================
            // 1. IDENTIFICAR A LOJA E CONFIGURAR O SELETOR
            // =============================================
            $config = $this->identificarLoja($url);

            // =============================================
            // 2. ACESSAR A PÁGINA (Com disfarce + timeout + retry)
            // =============================================
            $html = $this->buscarPagina($url);

            if (!$html) {
                $this->resultados[] = [$componente->nome, '❌ Erro HTTP', '-', '-'];
                return;
            }

            $crawler = new Crawler($html);

            // =============================================
            // 3. EXTRAIR O PREÇO
            // =============================================
            $precoFloat = $this->extrairPreco($crawler, $url, $config);

            if ($precoFloat === null || $precoFloat <= 0) {
                $this->warn("   ⚠ Preço não encontrado ou inválido.");
                $this->resultados[] = [$componente->nome, '⚠ Preço inválido', $config['moeda'], '-'];
                return;
            }

            // =============================================
            // 4. SALVAR PREÇO (evitando duplicatas)
            // =============================================
            $this->salvarPreco($componente, $precoFloat, $config['moeda']);

            // =============================================
            // 5. VERIFICAR E DISPARAR ALERTAS
            // =============================================
            $alertasEnviados = $this->dispararAlertas($componente, $precoFloat);

            $moedaSimbolo = $config['moeda'] === 'USD' ? 'US$' : 'R$';
            $this->resultados[] = [
                $componente->nome,
                '✅ OK',
                "{$moedaSimbolo} {$precoFloat}",
                $alertasEnviados > 0 ? "📧 {$alertasEnviados}" : '-'
            ];

        } catch (\Exception $e) {
            $this->error("   ❌ Erro: {$e->getMessage()}");
            $this->resultados[] = [$componente->nome, '❌ Erro', '-', '-'];
        }
    }

    /**
     * Identifica a loja pelo URL e retorna a configuração de scraping
     */
    private function identificarLoja(string $url): array
    {
        // Mercado Livre
        if (str_contains($url, 'mercadolivre.com.br') || str_contains($url, 'mercadolibre.com')) {
            return [
                'loja' => 'Mercado Livre',
                'estrategia' => 'mercadolivre',
                'moeda' => 'BRL',
            ];
        }

        // Webscraper.io (loja de teste em dólar)
        if (str_contains($url, 'webscraper.io')) {
            return [
                'loja' => 'Webscraper.io',
                'estrategia' => 'webscraper',
                'seletor' => '.caption > .price',
                'moeda' => 'USD',
            ];
        }

        // BoaDica
        if (str_contains($url, 'boadica.com.br')) {
            return [
                'loja' => 'BoaDica',
                'estrategia' => 'boadica',
                'moeda' => 'BRL',
            ];
        }

        // Loja-teste local (Tech-Watch)
        if (str_contains($url, 'localhost') || str_contains($url, 'loja-teste') || str_contains($url, '127.0.0.1')) {
            return [
                'loja' => 'Loja Teste',
                'estrategia' => 'seletor_css',
                'seletor' => '.price_vista',
                'moeda' => 'BRL',
            ];
        }

        // Fallback — tenta seletores genéricos
        return [
            'loja' => 'Desconhecida',
            'estrategia' => 'generica',
            'moeda' => 'BRL',
        ];
    }

    /**
     * Faz a requisição HTTP com User-Agent, timeout e retry
     */
    private function buscarPagina(string $url): ?string
    {
        $resposta = Http::withoutVerifying()
            ->timeout(15)
            ->retry(2, 1000)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Cache-Control' => 'max-age=0',
            ])
            ->get($url);

        if (!$resposta->successful()) {
            $this->error("   ❌ Erro HTTP {$resposta->status()} ao acessar: {$url}");
            return null;
        }

        return $resposta->body();
    }

    /**
     * Extrai o preço do HTML usando a estratégia correta para cada loja
     */
    private function extrairPreco(Crawler $crawler, string $url, array $config): ?float
    {
        $textoPreco = null;

        switch ($config['estrategia']) {

            // ─── MERCADO LIVRE ───
            // Estratégia 1: Tentar <meta itemprop="price"> (mais estável)
            // Estratégia 2: Montar fração + centavos dos spans
            // Estratégia 3: Pegar do JSON-LD embutido na página
            case 'mercadolivre':
                $textoPreco = $this->extrairPrecoMercadoLivre($crawler);
                break;

            // ─── BOADICA ───
            // Procura span que contenha "R$"
            case 'boadica':
                $node = $crawler->filterXPath('//span[contains(., "R$")]')->first();
                if ($node->count() > 0) {
                    $textoPreco = $node->text();
                }
                break;

            // ─── LOJAS COM SELETOR CSS DIRETO ───
            case 'seletor_css':
            case 'webscraper':
                $seletor = $config['seletor'];
                $node = $crawler->filter($seletor)->first();
                if ($node->count() > 0) {
                    $textoPreco = $node->text();
                }
                break;

            // ─── LOJA DESCONHECIDA (FALLBACK GENÉRICO) ───
            case 'generica':
                $textoPreco = $this->extrairPrecoGenerico($crawler);
                break;
        }

        if (!$textoPreco) {
            return null;
        }

        $this->line("   📄 Texto encontrado: \"{$textoPreco}\"");

        // Limpar e converter para float
        return $this->limparPreco($textoPreco, $config['moeda']);
    }

    /**
     * Estratégia especial para Mercado Livre (3 tentativas em cascata)
     */
    private function extrairPrecoMercadoLivre(Crawler $crawler): ?string
    {
        // Tentativa 1: Meta tag itemprop="price" (mais estável)
        $meta = $crawler->filter('meta[itemprop="price"]');
        if ($meta->count() > 0) {
            $valor = $meta->first()->attr('content');
            if ($valor && is_numeric($valor)) {
                $this->line('   ✓ Preço via meta itemprop (estável)');
                return $valor;
            }
        }

        // Tentativa 2: JSON-LD embutido na página
        $scripts = $crawler->filter('script[type="application/ld+json"]');
        foreach ($scripts as $script) {
            $json = json_decode($script->textContent, true);
            if ($json && isset($json['offers']['price'])) {
                $this->line('   ✓ Preço via JSON-LD');
                return (string) $json['offers']['price'];
            }
            // Algumas páginas usam array de offers
            if ($json && isset($json['offers'][0]['price'])) {
                $this->line('   ✓ Preço via JSON-LD (array)');
                return (string) $json['offers'][0]['price'];
            }
        }

        // Tentativa 3: Montar fração + centavos dos spans
        $fracaoNode = $crawler->filter('.andes-money-amount__fraction');
        if ($fracaoNode->count() > 0) {
            $fracao = trim($fracaoNode->first()->text());
            $centavos = '00';

            $centavosNode = $crawler->filter('.andes-money-amount__cents');
            if ($centavosNode->count() > 0) {
                $centavos = trim($centavosNode->first()->text());
            }

            $this->line("   ✓ Preço via spans: {$fracao},{$centavos}");
            return "{$fracao}.{$centavos}";
        }

        return null;
    }

    /**
     * Fallback genérico: tenta vários seletores comuns de preço
     */
    private function extrairPrecoGenerico(Crawler $crawler): ?string
    {
        $seletoresComuns = [
            'meta[itemprop="price"]',          // Schema.org
            '[class*="price"]:not(script)',     // Qualquer classe com "price"
            '[class*="preco"]:not(script)',     // Qualquer classe com "preco"
            '[class*="valor"]:not(script)',     // Qualquer classe com "valor"
            '.product-price',                   // Padrão comum
            '.sale-price',                      // Preço em promoção
            '#price',                           // ID price
        ];

        foreach ($seletoresComuns as $seletor) {
            try {
                // Meta tag retorna content, não text
                if (str_starts_with($seletor, 'meta')) {
                    $node = $crawler->filter($seletor);
                    if ($node->count() > 0) {
                        $valor = $node->first()->attr('content');
                        if ($valor && preg_match('/[\d]/', $valor)) {
                            $this->line("   ✓ Preço encontrado via: {$seletor}");
                            return $valor;
                        }
                    }
                    continue;
                }

                $node = $crawler->filter($seletor)->first();
                if ($node->count() > 0) {
                    $texto = trim($node->text());
                    if (preg_match('/[\d]/', $texto)) {
                        $this->line("   ✓ Preço encontrado via: {$seletor}");
                        return $texto;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Limpa o texto do preço e converte para float
     */
    private function limparPreco(string $textoPreco, string $moeda): ?float
    {
        // Se já é um número puro (ex: veio de meta tag ou JSON-LD)
        if (is_numeric($textoPreco)) {
            return round((float) $textoPreco, 2);
        }

        if ($moeda === 'USD') {
            // Formato americano: $ 1,299.99 → 1299.99
            $limpo = preg_replace('/[^0-9.]/', '', $textoPreco);
        } else {
            // Formato brasileiro: R$ 1.599,00 → 1599.00
            // 1. Remove tudo que não é dígito, ponto ou vírgula
            $limpo = preg_replace('/[^0-9.,]/', '', $textoPreco);
            // 2. Remove pontos de separador de milhar (ex: 1.599 → 1599)
            //    O ponto é milhar se houver vírgula depois OU se tiver 3 dígitos após o ponto
            if (str_contains($limpo, ',')) {
                // Formato BR clássico: "1.599,00"
                $limpo = str_replace('.', '', $limpo);  // Remove milhar
                $limpo = str_replace(',', '.', $limpo); // Vírgula vira ponto decimal
            } else {
                // Sem vírgula — pode ser "1599" ou "1599.00"
                // Se tiver ponto com exatamente 2 dígitos depois, é decimal
                if (preg_match('/\.\d{2}$/', $limpo)) {
                    // Ex: "1599.00" → já está ok
                } else {
                    // Ex: "1.599" → remover pontos de milhar
                    $limpo = str_replace('.', '', $limpo);
                }
            }
        }

        $valor = (float) $limpo;
        return $valor > 0 ? round($valor, 2) : null;
    }

    /**
     * Salva o preço no histórico, evitando duplicatas
     */
    private function salvarPreco(ComponenteHardware $componente, float $precoFloat, string $moeda): void
    {
        $moedaSimbolo = $moeda === 'USD' ? 'US$' : 'R$';

        // Verifica se o último preço registrado é igual ao atual (evita duplicata)
        $ultimoHistorico = $componente->historicosPreco()
            ->orderBy('registrado_em', 'desc')
            ->first();

        if ($ultimoHistorico && (float) $ultimoHistorico->preco === $precoFloat) {
            $this->line("   ℹ Preço não mudou ({$moedaSimbolo} {$precoFloat}), pulando registro.");
            // Atualiza o preço atual mesmo assim para manter sincronizado
            $componente->update(['preco_atual' => $precoFloat]);
            $componente->atualizarExtremos($precoFloat);
            return;
        }

        // Preço mudou ou é o primeiro registro → salvar
        HistoricoPreco::create([
            'componente_hardware_id' => $componente->id,
            'preco' => $precoFloat,
        ]);

        $componente->update(['preco_atual' => $precoFloat]);
        $componente->atualizarExtremos($precoFloat);

        $this->info("   💰 Novo preço salvo: {$moedaSimbolo} {$precoFloat}");
    }

    /**
     * Dispara alertas para inscrições cujo preço-alvo foi atingido
     * Retorna a quantidade de alertas disparados
     */
    private function dispararAlertas(ComponenteHardware $componente, float $precoFloat): int
    {
        // Busca apenas alertas PENDENTES (nunca notificados) cujo alvo foi atingido
        $inscricoes = $componente->inscricoesAlerta()
            ->pendentes()
            ->where('preco_alvo', '>=', $precoFloat)
            ->get();

        $enviados = 0;

        foreach ($inscricoes as $inscricao) {
            try {
                Notification::route('mail', $inscricao->email_usuario)
                    ->notify(new AlertaPrecoBaixo($componente, $precoFloat));

                // Marca como notificado para não enviar de novo
                $inscricao->update(['notificado_em' => now()]);

                $this->info("   📧 Alerta enviado para: {$inscricao->email_usuario}");
                $enviados++;
            } catch (\Exception $e) {
                $this->error("   ❌ Falha ao enviar e-mail para {$inscricao->email_usuario}: {$e->getMessage()}");
            }
        }

        return $enviados;
    }

    /**
     * Exibe tabela de resumo com resultado de cada componente
     */
    private function exibirResumo(): void
    {
        $this->info('');
        $this->info('═══════════════════════════════════════════');
        $this->info('              📊 RESUMO');
        $this->info('═══════════════════════════════════════════');

        $this->table(
            ['Componente', 'Status', 'Preço', 'Alertas'],
            $this->resultados
        );
    }
}