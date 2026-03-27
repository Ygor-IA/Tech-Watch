<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Mostra a tela de Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Processa o Login
    public function login(Request $request)
    {
        $credenciais = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credenciais)) {
            $request->session()->regenerate();
            return redirect()->route('componentes.index')->with('sucesso', 'Login realizado com sucesso!');
        }

        return back()->withErrors(['email' => 'As credenciais fornecidas estão incorretas.'])->onlyInput('email');
    }

    // Mostra a tela de Cadastro
    public function showRegister()
    {
        return view('auth.register');
    }

    // Processa o Cadastro
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Exige confirmação de senha
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Criptografa a senha para segurança
        ]);

        Auth::login($user); // Loga o usuário automaticamente após o cadastro

        return redirect()->route('componentes.index')->with('sucesso', 'Cadastro realizado com sucesso! Bem-vindo ao Tech-Watch.');
    }

    // Faz o Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}