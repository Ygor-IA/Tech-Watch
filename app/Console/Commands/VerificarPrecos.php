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
                // 1. Acessa o link ignorando verificação de certificado local
                $resposta = Http::withoutVerifying()->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9',
                    'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                ])->get($componente->link);

                // Se falhar, imprime o CÓDIGO DO ERRO exato
                if (!$resposta->successful()) {
                    $this->error("Erro " . $resposta->status() . " ao acessar: {$componente->link}");
                    continue;
                }

                // 2. Carrega o HTML da página no Crawler
                $html = $resposta->body();
                $crawler = new Crawler($html);

                // 3. CAPTURA DO PREÇO usando seletor curinga
                $textoPreco = $crawler->filter('[class*="price_vista"]')->first()->text();

                // 4. Limpeza do dado (Transforma "R$ 1.500,00" em 1500.00)
                $precoLimpo = preg_replace('/[^0-9,]/', '', $textoPreco); // Tira R$ e pontos
                $precoLimpo = str_replace(',', '.', $precoLimpo); // Troca vírgula por ponto para o banco
                $precoFloat = (float) $precoLimpo;

                // 5. Salva no banco de dados se conseguiu um preço válido
                if ($precoFloat > 0) {
                    HistoricoPreco::create([
                        'componente_hardware_id' => $componente->id,
                        'preco' => $precoFloat,
                    ]);

                    $componente->update(['preco_atual' => $precoFloat]);
                    $this->info("Novo preço salvo: R$ {$precoFloat}");

                    // === NOVA LÓGICA DE ALERTAS ===
                    // Busca todos os usuários que pediram alerta para este componente 
                    // e que o preço alvo seja MAIOR ou IGUAL ao preço atual da loja
                    $inscricoes = $componente->inscricoesAlerta()->where('preco_alvo', '>=', $precoFloat)->get();

                    foreach ($inscricoes as $inscricao) {
                        // Dispara o e-mail para cada usuário interessado
                        Notification::route('mail', $inscricao->email_usuario)
                            ->notify(new AlertaPrecoBaixo($componente, $precoFloat));
                            
                        $this->info("E-mail de alerta enviado para: {$inscricao->email_usuario}");
                    }
                }

            } catch (\Exception $e) {
                // Se a classe CSS não for encontrada na loja, o sistema avisa mas não trava
                $this->error("Não foi possível encontrar o preço para {$componente->nome}. Verifique a classe CSS.");
            }
        }

        $this->info('Verificação concluída!');
    }
}