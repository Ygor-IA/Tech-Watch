<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComponenteHardwareController;

Route::get('/', function () {
    // Redireciona a página inicial direto para a lista de hardwares
    return redirect()->route('componentes.index'); 
});

// Essa única linha cria magicamente todas as 7 rotas do CRUD
Route::resource('componentes', ComponenteHardwareController::class);
// Rota para receber os dados do formulário de alerta
Route::post('/componentes/{id}/alerta', [ComponenteHardwareController::class, 'assinarAlerta'])->name('componentes.alerta');