<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComponenteHardwareController;
use App\Http\Controllers\AuthController;

// Redireciona a página inicial para a lista de componentes
Route::get('/', function () {
    return redirect()->route('componentes.index');
});

// ==========================================
// ROTAS DE AUTENTICAÇÃO (Abertas para todos)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/cadastro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/cadastro', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// ROTAS PROTEGIDAS (Apenas usuários logados)
// ==========================================
Route::middleware('auth')->group(function () {
    // Todas as 7 rotas do seu CRUD agora estão protegidas!
    Route::resource('componentes', ComponenteHardwareController::class);
    
    // A rota de criar o alerta de preço (se for manter para a apresentação)
    Route::post('/componentes/{id}/alerta', [ComponenteHardwareController::class, 'assinarAlerta'])->name('componentes.alerta');
});
// ==========================================
// ROTAS ABERTAS E TESTES
// ==========================================
Route::get('/loja-teste', function () {
    return view('loja-teste'); // Substitua pelo nome real do arquivo da sua loja fake
});