<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_edit_and_delete_a_plan(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('planes.store'), [
            'nombre' => 'Ilimitado Total',
            'precio' => 399.50,
            'descripcion' => 'Datos, llamadas y SMS ilimitados.',
            'activo' => true,
            'orden' => 1,
        ])->assertRedirect(route('planes.index'));

        $plan = Plan::where('nombre', 'Ilimitado Total')->firstOrFail();
        $this->assertSame(39950, $plan->precio);
        $this->assertTrue($plan->activo);

        $this->actingAs($admin)->put(route('planes.update', $plan), [
            'nombre' => 'Ilimitado Total Plus',
            'precio' => 449.00,
            'descripcion' => $plan->descripcion,
            'activo' => false,
            'orden' => 2,
        ])->assertRedirect(route('planes.index'));

        $plan->refresh();
        $this->assertSame('Ilimitado Total Plus', $plan->nombre);
        $this->assertFalse($plan->activo);

        $this->actingAs($admin)->delete(route('planes.destroy', $plan))
            ->assertRedirect(route('planes.index'));

        $this->assertDatabaseMissing('planes', ['id' => $plan->id]);
    }
}
