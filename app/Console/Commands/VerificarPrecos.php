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
    // O comando que você vai digitar no terminal para rodar o robô
    protected $signature = 'precos:verificar';

    protected $description = 'Entra no link de cada hardware, raspa o preço atual e salva no histórico.';

    public function handle()
    {
        $this->info('Iniciando a verificação de preços...');

        $componentes = ComponenteHardware::all();

        foreach ($componentes as $componente) {
            $this->info("Verificando: {$componente->nome}");

            try {
                $url = $componente->link;
                $seletor = '';
                $tipoMoeda = 'BRL'; // Padrão é Real

                // =======================================================
                // 1. O CÉREBRO DO ROBÔ (Dicionário de Lojas)
                // Ele olha o link e decide qual classe CSS usar
                // =======================================================
                if (str_contains($url, 'mercadolivre.com.br')) {
                    $seletor = '.andes-money-amount__fraction';
                } elseif (str_contains($url, 'webscraper.io')) {
                    $seletor = '.caption > .price';
                    $tipoMoeda = 'USD'; // Essa loja é em dólar
                } elseif (str_contains($url, 'boadica.com.br')) {
                    $seletor = '.preco'; // Classe genérica de exemplo para o BoaDica
                } else {
                    // Curinga: Se for uma loja desconhecida, tenta adivinhar
                    $seletor = '[class*="price"]';
                }

                // =======================================================
                // 2. O ACESSO (Com o "Super Disfarce")
                // =======================================================
                $resposta = Http::withoutVerifying()->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9',
                    'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                ])->get($url);

                if (!$resposta->successful()) {
                    $this->error("Erro " . $resposta->status() . " na loja: {$url}");
                    continue; // Pula para o próximo hardware
                }

                $html = $resposta->body();
                $crawler = new \Symfony\Component\DomCrawler\Crawler($html);

                // =======================================================
                // 3. A CAPTURA E LIMPEZA INTELIGENTE
                // =======================================================
                
                if (str_contains($url, 'boadica.com.br')) {
                    // Tática Sniper (XPath): Procura o primeiro span que contenha "R$"
                    $textoPreco = $crawler->filterXPath('//span[contains(., "R$")]')->first()->text();
                } else {
                    // Lojas normais (Mercado Livre, Webscraper) usam o seletor CSS
                    $textoPreco = $crawler->filter($seletor)->first()->text();
                }

                if ($tipoMoeda == 'USD') {
                    // Limpeza para sites em Dólar (ex: $ 120.99)
                    $precoLimpo = preg_replace('/[^0-9.]/', '', $textoPreco); 
                } else {
                    // Limpeza para sites em Real (ex: R$ 1.500,00)
                    $precoLimpo = preg_replace('/[^0-9,]/', '', $textoPreco); 
                    $precoLimpo = str_replace(',', '.', $precoLimpo); 
                }
                
                $precoFloat = (float) $precoLimpo;

                // =======================================================
                // 4. SALVANDO E DISPARANDO ALE RTAS
                // =======================================================
                if ($precoFloat > 0) {
                    HistoricoPreco::create([
                        'componente_hardware_id' => $componente->id,
                        'preco' => $precoFloat,
                    ]);

                    $componente->update(['preco_atual' => $precoFloat]);
                    $this->info("Novo preço salvo: R$ {$precoFloat}");

                    // Lógica de envio de e-mail...
                    $inscricoes = $componente->inscricoesAlerta()->where('preco_alvo', '>=', $precoFloat)->get();
                    foreach ($inscricoes as $inscricao) {
                        \Illuminate\Support\Facades\Notification::route('mail', $inscricao->email_usuario)
                            ->notify(new \App\Notifications\AlertaPrecoBaixo($componente, $precoFloat));
                        $this->info("E-mail enviado para: {$inscricao->email_usuario}");
                    }
                }

            } catch (\Exception $e) {
                $this->error("Não foi possível encontrar o preço em {$componente->nome}. O layout da loja pode ter mudado.");
            }
        }

        $this->info('Verificação concluída!');
    }
}