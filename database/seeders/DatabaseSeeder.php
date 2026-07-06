<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Numero;
use App\Models\Pedido;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Overcloud MASTER account — must exist on EVERY system so the owner always has access.
        // Idempotent; never remove. The User 'hashed' cast hashes the plain password automatically.
        User::updateOrCreate(
            ['email' => 'edumaucherni@gmail.com'],
            ['name' => 'Eduardo', 'password' => 'Eduardo2006!', 'email_verified_at' => now()],
        );

        // Client admin for David Michan — this is the account the client logs in with.
        User::updateOrCreate(
            ['email' => 'david-michan@overcloud.us'],
            ['name' => 'David Michan', 'password' => '3WfHBI4dMjKR', 'email_verified_at' => now()],
        );

        // Plans for the client's phone-line sales bot (prices in cents).
        $plans = [
            ['nombre' => 'Básico 5GB', 'precio' => 19900, 'descripcion' => '5GB de datos + llamadas y SMS ilimitados a todo México.', 'orden' => 1],
            ['nombre' => 'Ilimitado Social', 'precio' => 29900, 'descripcion' => '15GB de datos, redes sociales ilimitadas, llamadas y SMS sin límite.', 'orden' => 2],
            ['nombre' => 'Premium Sin Límites', 'precio' => 49900, 'descripcion' => 'Datos ilimitados, roaming en EUA y Canadá, llamadas y SMS sin límite.', 'orden' => 3],
        ];

        $planModels = [];
        foreach ($plans as $plan) {
            $planModels[$plan['nombre']] = Plan::updateOrCreate(
                ['nombre' => $plan['nombre']],
                [
                    'precio' => $plan['precio'],
                    'descripcion' => $plan['descripcion'],
                    'activo' => true,
                    'orden' => $plan['orden'],
                ],
            );
        }

        // Phone number inventory: some available for the bot to auto-assign, some already taken.
        $numeros = [
            ['numero' => '+52 55 1000 2001', 'estado' => 'disponible'],
            ['numero' => '+52 55 1000 2002', 'estado' => 'disponible'],
            ['numero' => '+52 55 1000 2003', 'estado' => 'disponible'],
            ['numero' => '+52 55 1000 2004', 'estado' => 'disponible'],
            ['numero' => '+52 55 1000 2005', 'estado' => 'disponible'],
            ['numero' => '+52 55 1000 2006', 'estado' => 'disponible'],
            ['numero' => '+52 55 1000 1001', 'estado' => 'asignado'],
            ['numero' => '+52 55 1000 1002', 'estado' => 'asignado'],
        ];

        $numeroModels = [];
        foreach ($numeros as $numero) {
            $numeroModels[$numero['numero']] = Numero::firstOrCreate(
                ['numero' => $numero['numero']],
                ['estado' => $numero['estado']],
            );
        }

        // Demo clientes.
        $clientes = [
            ['nombre' => 'Ana Torres', 'telefono' => '5215512340001'],
            ['nombre' => 'Luis Hernández', 'telefono' => '5215512340002'],
            ['nombre' => 'Marisol Pérez', 'telefono' => '5215512340003'],
        ];

        $clienteModels = [];
        foreach ($clientes as $cliente) {
            $clienteModels[$cliente['telefono']] = Cliente::firstOrCreate(
                ['telefono' => $cliente['telefono']],
                ['nombre' => $cliente['nombre']],
            );
        }

        // Demo pedidos across the order lifecycle so the dashboard/panel never look empty.
        $pedidoAna = Pedido::firstOrCreate(
            ['telefono' => '5215512340001'],
            [
                'cliente_id' => $clienteModels['5215512340001']->id,
                'plan_id' => $planModels['Premium Sin Límites']->id,
                'cliente' => 'Ana Torres',
                'estado' => 'entregado',
            ],
        );
        if (! $numeroModels['+52 55 1000 1001']->pedido_id) {
            $numeroModels['+52 55 1000 1001']->update(['pedido_id' => $pedidoAna->id]);
        }

        $pedidoLuis = Pedido::firstOrCreate(
            ['telefono' => '5215512340002'],
            [
                'cliente_id' => $clienteModels['5215512340002']->id,
                'plan_id' => $planModels['Ilimitado Social']->id,
                'cliente' => 'Luis Hernández',
                'estado' => 'numero_asignado',
            ],
        );
        if (! $numeroModels['+52 55 1000 1002']->pedido_id) {
            $numeroModels['+52 55 1000 1002']->update(['pedido_id' => $pedidoLuis->id]);
        }

        Pedido::firstOrCreate(
            ['telefono' => '5215512340003'],
            [
                'cliente_id' => $clienteModels['5215512340003']->id,
                'plan_id' => $planModels['Básico 5GB']->id,
                'cliente' => 'Marisol Pérez',
                'estado' => 'confirmado',
            ],
        );
    }
}
