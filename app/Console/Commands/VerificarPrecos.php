<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ComponenteHardware;
use App\Models\HistoricoPreco;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

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
                // 1. Acessa o link da loja
                $resposta = Http::get($componente->link);

                // Se a página não carregar, pula para o próximo
                if (!$resposta->successful()) {
                    $this->error("Falha ao acessar o link: {$componente->link}");
                    continue;
                }

                // 2. Carrega o HTML da página no Crawler
                $html = $resposta->body();
                $crawler = new Crawler($html);

                // 3. CAPTURA DO PREÇO (Atenção aqui!)
                // Substitua '.preco-produto' pela classe real do HTML da loja (ex: Kabum, Pichau)
                // Você descobre isso clicando com o botão direito no preço na loja e indo em "Inspecionar"
                $textoPreco = $crawler->filter('.preco-produto')->first()->text();

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

                    // Atualiza o preço atual na tabela principal
                    $componente->update(['preco_atual' => $precoFloat]);

                    $this->info("Novo preço salvo: R$ {$precoFloat}");
                    
                    // TODO futuro: Aqui você chamaria a lógica de disparar e-mails para quem assinou o alerta!
                }

            } catch (\Exception $e) {
                // Se a classe CSS não for encontrada na loja, o sistema avisa mas não trava
                $this->error("Não foi possível encontrar o preço para {$componente->nome}. Verifique a classe CSS.");
            }
        }

        $this->info('Verificação concluída!');
    }
}