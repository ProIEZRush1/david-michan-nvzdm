<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(Request $request): Response
    {
        $planes = Plan::withCount('pedidos')
            ->when($request->string('buscar')->toString(), fn ($q, $buscar) => $q->where('nombre', 'like', "%{$buscar}%"))
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->map(fn (Plan $plan) => [
                'id' => $plan->id,
                'nombre' => $plan->nombre,
                'precio' => $plan->precio,
                'precio_formato' => '$'.number_format($plan->precio / 100, 2).' MXN',
                'descripcion' => $plan->descripcion,
                'activo' => $plan->activo,
                'orden' => $plan->orden,
                'pedidos_count' => $plan->pedidos_count,
            ]);

        return Inertia::render('Planes/Index', [
            'planes' => $planes,
            'buscar' => $request->string('buscar')->toString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Planes/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Plan::create([
            'nombre' => $data['nombre'],
            'precio' => (int) round($data['precio'] * 100),
            'descripcion' => $data['descripcion'],
            'activo' => $data['activo'],
            'orden' => $data['orden'],
        ]);

        return redirect()->route('planes.index')->with('success', 'Plan creado correctamente.');
    }

    public function edit(Plan $plan): Response
    {
        return Inertia::render('Planes/Edit', [
            'plan' => [
                'id' => $plan->id,
                'nombre' => $plan->nombre,
                'precio' => $plan->precio / 100,
                'descripcion' => $plan->descripcion,
                'activo' => $plan->activo,
                'orden' => $plan->orden,
            ],
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validated($request);

        $plan->update([
            'nombre' => $data['nombre'],
            'precio' => (int) round($data['precio'] * 100),
            'descripcion' => $data['descripcion'],
            'activo' => $data['activo'],
            'orden' => $data['orden'],
        ]);

        return redirect()->route('planes.index')->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('planes.index')->with('success', 'Plan eliminado.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'activo' => ['boolean'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['activo'] = $request->boolean('activo');
        $data['orden'] = $request->integer('orden');

        return $data;
    }
}
