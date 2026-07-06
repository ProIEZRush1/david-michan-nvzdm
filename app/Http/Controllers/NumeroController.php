<?php

namespace App\Http\Controllers;

use App\Models\Numero;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NumeroController extends Controller
{
    public function index(Request $request): Response
    {
        $numeros = Numero::with('pedido.plan')
            ->when($request->string('buscar')->toString(), fn ($q, $buscar) => $q->where('numero', 'like', "%{$buscar}%"))
            ->orderByDesc('id')
            ->get()
            ->map(fn (Numero $numero) => [
                'id' => $numero->id,
                'numero' => $numero->numero,
                'estado' => $numero->estado,
                'pedido' => $numero->pedido ? [
                    'id' => $numero->pedido->id,
                    'cliente' => $numero->pedido->cliente,
                    'plan' => $numero->pedido->plan?->nombre,
                ] : null,
            ]);

        return Inertia::render('Numeros/Index', [
            'numeros' => $numeros,
            'buscar' => $request->string('buscar')->toString(),
            'disponibles' => Numero::where('estado', 'disponible')->count(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Numeros/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'numero' => ['required', 'string', 'max:30', 'unique:numeros,numero'],
        ]);

        Numero::create(['numero' => $data['numero'], 'estado' => 'disponible']);

        return redirect()->route('numeros.index')->with('success', 'Número agregado al inventario.');
    }

    public function edit(Numero $numero): Response
    {
        return Inertia::render('Numeros/Edit', [
            'numero' => [
                'id' => $numero->id,
                'numero' => $numero->numero,
                'estado' => $numero->estado,
            ],
        ]);
    }

    public function update(Request $request, Numero $numero): RedirectResponse
    {
        $data = $request->validate([
            'numero' => ['required', 'string', 'max:30', 'unique:numeros,numero,'.$numero->id],
            'estado' => ['required', 'in:disponible,asignado'],
        ]);

        $numero->update($data);

        return redirect()->route('numeros.index')->with('success', 'Número actualizado correctamente.');
    }

    public function destroy(Numero $numero): RedirectResponse
    {
        $numero->delete();

        return redirect()->route('numeros.index')->with('success', 'Número eliminado del inventario.');
    }
}
