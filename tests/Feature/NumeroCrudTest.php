<?php

namespace Tests\Feature;

use App\Models\Numero;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NumeroCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_edit_and_delete_a_number(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('numeros.store'), [
            'numero' => '+52 55 4444 5555',
        ])->assertRedirect(route('numeros.index'));

        $numero = Numero::where('numero', '+52 55 4444 5555')->firstOrFail();
        $this->assertSame('disponible', $numero->estado);

        $this->actingAs($admin)->put(route('numeros.update', $numero), [
            'numero' => $numero->numero,
            'estado' => 'asignado',
        ])->assertRedirect(route('numeros.index'));

        $numero->refresh();
        $this->assertSame('asignado', $numero->estado);

        $this->actingAs($admin)->delete(route('numeros.destroy', $numero))
            ->assertRedirect(route('numeros.index'));

        $this->assertDatabaseMissing('numeros', ['id' => $numero->id]);
    }
}
