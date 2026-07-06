<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClienteController extends Controller
{
    public function index(Request $request): Response
    {
        $clientes = Cliente::withCount('pedidos')
            ->when($request->string('buscar')->toString(), fn ($q, $buscar) => $q
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('telefono', 'like', "%{$buscar}%"))
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'buscar' => $request->string('buscar')->toString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Clientes/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente): Response
    {
        return Inertia::render('Clientes/Edit', [
            'cliente' => $cliente->only(['id', 'nombre', 'telefono']),
        ]);
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($this->validated($request, $cliente->id));

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nombre' => ['nullable', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:30', 'unique:clientes,telefono'.($ignoreId ? ",{$ignoreId}" : '')],
        ]);
    }
}
