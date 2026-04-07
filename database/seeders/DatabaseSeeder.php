<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ComponenteHardware;
use App\Models\HistoricoPreco;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Cria usuário de demonstração ADM
        $user = User::firstOrCreate([
            'email' => 'admin@techwatch.com'
        ], [
            'name' => 'Admin Tech-Watch',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $this->command->info('Usuário ADM de teste criado (admin@techwatch.com - senha: password123)');

        // 1.1 Cria dois usuários comuns
        $user1 = User::firstOrCreate([
            'email' => 'joao@techwatch.com'
        ], [
            'name' => 'João',
            'password' => Hash::make('password123'),
            'is_admin' => false,
        ]);

        $user2 = User::firstOrCreate([
            'email' => 'maria@techwatch.com'
        ], [
            'name' => 'Maria',
            'password' => Hash::make('password123'),
            'is_admin' => false,
        ]);

        $this->command->info('Dois usuários comuns criados (joao@ e maria@ - senha: password123)');

        // 2. Componentes de teste com links reais (BoaDica, ML e Teste Local)
        $componentes = [
            [
                'nome' => 'Radeon RX 7600 (Exemplo Mercado Livre)',
                'categoria' => 'GPU',
                'link' => 'https://www.mercadolivre.com.br/placa-de-video-amd-radeon-rx-7600-8gb-gddr6-gigabyte-cor-preto/p/MLB24376378',
                'preco_atual' => 1999.00,
                'ativo' => true,
            ],
            [
                'nome' => 'Processador Intel Core i5-12400F',
                'categoria' => 'CPU',
                'link' => 'https://www.boadica.com.br/produtos/p193635',
                'preco_atual' => 749.90,
                'ativo' => true,
            ],
            [
                'nome' => 'SSD WD Green SN350 1TB',
                'categoria' => 'SSD',
                'link' => 'https://www.boadica.com.br/produtos/p192569',
                'preco_atual' => 380.00,
                'ativo' => true,
            ],
            [
                'nome' => 'Monitor Lenovo ThinkVision (Teste WebScraper)',
                'categoria' => 'Monitor',
                'link' => 'https://webscraper.io/test-sites/e-commerce/allinone/computers/monitors',
                'preco_atual' => 134.99,
                'ativo' => false, // Pausado como exemplo
            ]
        ];

        foreach ($componentes as $compData) {
            $comp = ComponenteHardware::firstOrCreate(
                ['link' => $compData['link']], // Evitar duplo cadastro se rodar o seeder 2x
                array_merge($compData, ['user_id' => $user1->id])
            );

            // Popula com histórico fictício para os gráficos não ficarem vazios
            if ($comp->historicosPreco()->count() === 0) {
                // Cria 10 dias de histórico simulando queda de preços
                $precoBase = $comp->preco_atual;
                for ($i = 10; $i >= 0; $i--) {
                    // Simular preços mais altos no passado e caindo até o preço atual
                    $fator = 1.0 + ($i * 0.03); // Até 30% mais caro 10 dias atrás
                    $precoSimulado = round($precoBase * $fator, 2);
                    
                    HistoricoPreco::create([
                        'componente_hardware_id' => $comp->id,
                        'preco' => $precoSimulado,
                        'registrado_em' => Carbon::now()->subDays($i)->setTime(10, 0, 0)
                    ]);
                }
                
                // Atualiza também os extremos
                $comp->update([
                    'preco_minimo' => $comp->preco_atual,
                    'preco_maximo' => round($comp->preco_atual * 1.30, 2),
                ]);
            }
        }

        $this->command->info('Dados de demonstração inseridos com sucesso!');
    }
}
