<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PedidoController extends Controller
{
    public function index(Request $request): Response
    {
        $pedidos = Pedido::with(['plan', 'numero'])
            ->when($request->string('buscar')->toString(), fn ($q, $buscar) => $q
                ->where('cliente', 'like', "%{$buscar}%")
                ->orWhere('telefono', 'like', "%{$buscar}%"))
            ->when($request->string('estado')->toString(), fn ($q, $estado) => $q->where('estado', $estado))
            ->orderByDesc('id')
            ->get()
            ->map(fn (Pedido $pedido) => [
                'id' => $pedido->id,
                'cliente' => $pedido->cliente,
                'telefono' => $pedido->telefono,
                'plan' => $pedido->plan?->nombre,
                'numero' => $pedido->numero?->numero,
                'estado' => $pedido->estado,
                'created_at' => $pedido->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Pedidos/Index', [
            'pedidos' => $pedidos,
            'buscar' => $request->string('buscar')->toString(),
            'estado' => $request->string('estado')->toString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Pedidos/Create', [
            'planes' => Plan::where('activo', true)->orderBy('orden')->get(['id', 'nombre']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cliente' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:30'],
            'plan_id' => ['required', 'exists:planes,id'],
            'estado' => ['required', 'in:nuevo,confirmado,numero_asignado,entregado,cancelado'],
        ]);

        $cliente = Cliente::updateOrCreate(
            ['telefono' => $data['telefono']],
            ['nombre' => $data['cliente']],
        );

        Pedido::create([...$data, 'cliente_id' => $cliente->id]);

        return redirect()->route('pedidos.index')->with('success', 'Pedido creado correctamente.');
    }

    public function edit(Pedido $pedido): Response
    {
        return Inertia::render('Pedidos/Edit', [
            'pedido' => [
                'id' => $pedido->id,
                'cliente' => $pedido->cliente,
                'telefono' => $pedido->telefono,
                'plan_id' => $pedido->plan_id,
                'estado' => $pedido->estado,
                'numero' => $pedido->numero?->numero,
            ],
            'planes' => Plan::orderBy('orden')->get(['id', 'nombre']),
        ]);
    }

    public function update(Request $request, Pedido $pedido): RedirectResponse
    {
        $data = $request->validate([
            'cliente' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:30'],
            'plan_id' => ['required', 'exists:planes,id'],
            'estado' => ['required', 'in:nuevo,confirmado,numero_asignado,entregado,cancelado'],
        ]);

        $pedido->update($data);

        return redirect()->route('pedidos.index')->with('success', 'Pedido actualizado correctamente.');
    }

    public function destroy(Pedido $pedido): RedirectResponse
    {
        $pedido->delete();

        return redirect()->route('pedidos.index')->with('success', 'Pedido eliminado.');
    }
}
