<?php

namespace App\Http\Controllers;

use App\Models\ComponenteHardware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComponenteHardwareController extends Controller
{
    // [READ] Lista todos os componentes restritos ao usuário logado
    public function index()
    {
        // Pega apenas os componentes do usuário logado (ou todos caso seja admin)
        if (Auth::user()->email === 'admin@techwatch.com') {
            $componentes = ComponenteHardware::all();
        } else {
            $componentes = Auth::user()->componentes()->get();
        }
        return view('componentes.index', compact('componentes'));
    }

    // [CREATE] Mostra o formulário para adicionar novo hardware
    public function create()
    {
        $categorias = ComponenteHardware::CATEGORIAS;
        return view('componentes.create', compact('categorias'));
    }

    // [CREATE] Salva o novo hardware no banco de dados e vincula ao usuário
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:50',
            'link' => 'required|url',
            'preco_atual' => 'nullable|numeric'
        ]);

        // Vincula o componente ao usuário logado
        Auth::user()->componentes()->create($dados);

        // Salva na seed para ser recarregado caso o banco seja recriado
        $jsonPath = database_path('seeders/components_seed.json');
        $seededComponents = [];
        if (file_exists($jsonPath)) {
            $seededComponents = json_decode(file_get_contents($jsonPath), true) ?? [];
        }
        $seededComponents[] = array_merge($dados, [
            'ativo' => true,
            'user_email' => Auth::user()->email
        ]);
        file_put_contents($jsonPath, json_encode($seededComponents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('componentes.index')->with('sucesso', 'Componente adicionado com sucesso!');
    }

    // [READ] Mostra detalhes de um componente específico
    public function show(ComponenteHardware $componente)
    {
        // Autorização básica: usuário logado só pode ver seus próprios componentes (admin vê tudo)
        if ($componente->user_id !== Auth::id() && Auth::user()->email !== 'admin@techwatch.com') {
            abort(403, 'Acesso Negado');
        }

        // Carrega o histórico de preços ordenado por data
        $componente->load(['historicosPreco' => function($query) {
            $query->orderBy('registrado_em', 'asc');
        }]);

        return view('componentes.show', compact('componente'));
    }

    // [UPDATE] Mostra formulário de edição
    public function edit(ComponenteHardware $componente)
    {
        if ($componente->user_id !== Auth::id() && Auth::user()->email !== 'admin@techwatch.com') {
            abort(403, 'Acesso Negado');
        }

        $categorias = ComponenteHardware::CATEGORIAS;
        return view('componentes.edit', compact('componente', 'categorias'));
    }

    // [UPDATE] Salva as alterações no banco
    public function update(Request $request, ComponenteHardware $componente)
    {
        if ($componente->user_id !== Auth::id() && Auth::user()->email !== 'admin@techwatch.com') {
            abort(403, 'Acesso Negado');
        }

        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:50',
            'link' => 'required|url',
            'preco_atual' => 'nullable|numeric',
            'ativo' => 'boolean'
        ]);

        // Se não vier o 'ativo' no checkbox, significa que está desmarcado -> ativo=false
        $dados['ativo'] = $request->has('ativo');

        $componente->update($dados);

        return redirect()->route('componentes.index')->with('sucesso', 'Componente atualizado com sucesso!');
    }

    // [DELETE] Remove o hardware do banco (Soft Delete)
    public function destroy(ComponenteHardware $componente)
    {
        if ($componente->user_id !== Auth::id() && Auth::user()->email !== 'admin@techwatch.com') {
            abort(403, 'Acesso Negado');
        }

        $componente->delete();

        return redirect()->route('componentes.index')->with('sucesso', 'Componente removido!');
    }

    // [CREATE] Salva a inscrição de alerta de um usuário
    public function assinarAlerta(Request $request, ComponenteHardware $componente)
    {
        $request->validate([
            'preco_alvo' => 'required|numeric|min:0'
        ]);

        $componente->inscricoesAlerta()->create([
            'email_usuario' => Auth::user()->email, 
            'preco_alvo' => $request->preco_alvo
        ]);

        return back()->with('sucesso', 'Alerta criado, ' . Auth::user()->name . '! Avisaremos no seu e-mail assim que o preço cair.');
    }
}