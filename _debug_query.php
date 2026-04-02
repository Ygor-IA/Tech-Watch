<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$componentes = App\Models\ComponenteHardware::all();
foreach ($componentes as $c) {
    echo "ID: {$c->id} | Nome: {$c->nome} | Link: {$c->link} | Preço: {$c->preco_atual}\n";
}
echo "\nTotal: " . $componentes->count() . " componente(s)\n";
