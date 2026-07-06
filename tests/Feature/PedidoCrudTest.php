<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_edit_and_delete_a_pedido(): void
    {
        $admin = User::factory()->create();
        $plan = Plan::create(['nombre' => 'Básico 5GB', 'precio' => 19900, 'activo' => true, 'orden' => 1]);

        $this->actingAs($admin)->post(route('pedidos.store'), [
            'cliente' => 'Pedro Sánchez',
            'telefono' => '5215588880000',
            'plan_id' => $plan->id,
            'estado' => 'nuevo',
        ])->assertRedirect(route('pedidos.index'));

        $pedido = Pedido::where('telefono', '5215588880000')->firstOrFail();
        $this->assertSame('nuevo', $pedido->estado);
        $this->assertDatabaseHas('clientes', ['telefono' => '5215588880000', 'nombre' => 'Pedro Sánchez']);

        $this->actingAs($admin)->put(route('pedidos.update', $pedido), [
            'cliente' => $pedido->cliente,
            'telefono' => $pedido->telefono,
            'plan_id' => $plan->id,
            'estado' => 'entregado',
        ])->assertRedirect(route('pedidos.index'));

        $pedido->refresh();
        $this->assertSame('entregado', $pedido->estado);

        $this->actingAs($admin)->delete(route('pedidos.destroy', $pedido))
            ->assertRedirect(route('pedidos.index'));

        $this->assertDatabaseMissing('pedidos', ['id' => $pedido->id]);
    }
}
