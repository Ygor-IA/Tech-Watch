<?php

namespace App\Http\Controllers;

use App\Models\ComponenteHardware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComponenteHardwareController extends Controller
{
    // [READ] Lista todos os componentes (Tela inicial)
    public function index()
    {
        $componentes = ComponenteHardware::all();
        return view('componentes.index', compact('componentes'));
    }

    // [CREATE] Mostra o formulário para adicionar novo hardware
    public function create()
    {
        return view('componentes.create');
    }

    // [CREATE] Salva o novo hardware no banco de dados
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'link' => 'required|url',
            'preco_atual' => 'nullable|numeric'
        ]);

        ComponenteHardware::create($dados);

        return redirect()->route('componentes.index')->with('sucesso', 'Componente adicionado com sucesso!');
    }

    // [READ] Mostra detalhes de um componente específico (onde vai ficar o gráfico)
    public function show(ComponenteHardware $componente)
    {
        // Carrega o histórico de preços ordenado por data
        $componente->load(['historicosPreco' => function($query) {
            $query->orderBy('registrado_em', 'asc');
        }]);

        return view('componentes.show', compact('componente'));
    }

    // [UPDATE] Mostra formulário de edição
    public function edit(ComponenteHardware $componente)
    {
        return view('componentes.edit', compact('componente'));
    }

    // [UPDATE] Salva as alterações no banco
    public function update(Request $request, ComponenteHardware $componente)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'link' => 'required|url',
            'preco_atual' => 'nullable|numeric'
        ]);

        $componente->update($dados);

        return redirect()->route('componentes.index')->with('sucesso', 'Componente atualizado com sucesso!');
    }

    // [DELETE] Remove o hardware do banco
    public function destroy(ComponenteHardware $componente)
    {
        $componente->delete();

        return redirect()->route('componentes.index')->with('sucesso', 'Componente removido!');
    }

    // [CREATE] Salva a inscrição de alerta de um usuário
    public function assinarAlerta(Request $request, ComponenteHardware $componente)
    {
        $request->validate([
            'preco_alvo' => 'required|numeric|min:0'
        ]);

        // Puxamos o e-mail do usuário logado automaticamente
        $componente->inscricoesAlerta()->create([
            'email_usuario' => Auth::user()->email, 
            'preco_alvo' => $request->preco_alvo
        ]);

        return back()->with('sucesso', 'Alerta criado, ' . Auth::user()->name . '! Avisaremos no seu e-mail assim que o preço cair.');
    }
}