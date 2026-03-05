<?php

namespace App\Http\Controllers;

use App\Models\ComponenteHardware;
use Illuminate\Http\Request;

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
        // Validação simples para não entrar dado em branco
        $request->validate([
            'nome' => 'required|string|max:255',
            'link' => 'required|url',
            'preco_atual' => 'nullable|numeric'
        ]);

        ComponenteHardware::create($request->all());

        return redirect()->route('componentes.index')->with('sucesso', 'Componente adicionado com sucesso!');
    }

    // [READ] Mostra detalhes de um componente específico (onde vai ficar o gráfico)
    public function show($id)
    {
        // Busca o componente e traz junto todo o histórico de preços ordenado por data
        $componente = ComponenteHardware::with(['historicosPreco' => function($query) {
            $query->orderBy('registrado_em', 'asc');
        }])->findOrFail($id);

        return view('componentes.show', compact('componente'));
    }

    // [UPDATE] Mostra formulário de edição
    public function edit($id)
    {
        $componente = ComponenteHardware::findOrFail($id);
        return view('componentes.edit', compact('componente'));
    }

    // [UPDATE] Salva as alterações no banco
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'link' => 'required|url',
            'preco_atual' => 'nullable|numeric'
        ]);

        $componente = ComponenteHardware::findOrFail($id);
        $componente->update($request->all());

        return redirect()->route('componentes.index')->with('sucesso', 'Componente atualizado com sucesso!');
    }

    // [DELETE] Remove o hardware do banco
    public function destroy($id)
    {
        $componente = ComponenteHardware::findOrFail($id);
        $componente->delete();

        return redirect()->route('componentes.index')->with('sucesso', 'Componente removido!');
    }
}