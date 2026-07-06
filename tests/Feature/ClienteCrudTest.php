<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_edit_and_delete_a_cliente(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('clientes.store'), [
            'nombre' => 'Jorge Ramírez',
            'telefono' => '5215599990000',
        ])->assertRedirect(route('clientes.index'));

        $cliente = Cliente::where('telefono', '5215599990000')->firstOrFail();
        $this->assertSame('Jorge Ramírez', $cliente->nombre);

        $this->actingAs($admin)->put(route('clientes.update', $cliente), [
            'nombre' => 'Jorge Ramírez López',
            'telefono' => $cliente->telefono,
        ])->assertRedirect(route('clientes.index'));

        $cliente->refresh();
        $this->assertSame('Jorge Ramírez López', $cliente->nombre);

        $this->actingAs($admin)->delete(route('clientes.destroy', $cliente))
            ->assertRedirect(route('clientes.index'));

        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }
}
