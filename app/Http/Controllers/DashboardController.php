<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Numero;
use App\Models\Pedido;
use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'planes_activos' => Plan::where('activo', true)->count(),
                'pedidos_total' => Pedido::count(),
                'pedidos_pendientes' => Pedido::whereIn('estado', ['nuevo', 'confirmado'])->count(),
                'clientes_total' => Cliente::count(),
                'numeros_disponibles' => Numero::where('estado', 'disponible')->count(),
                'numeros_asignados' => Numero::where('estado', 'asignado')->count(),
            ],
        ]);
    }
}
