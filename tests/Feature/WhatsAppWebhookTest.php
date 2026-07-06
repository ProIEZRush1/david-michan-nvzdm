<?php

namespace Tests\Feature;

use App\Models\BotContact;
use App\Models\Numero;
use App\Models\Pedido;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function inbound(string $from, string $text): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/api/wa/inbound', [
            'from' => $from,
            'fromName' => 'Cliente de prueba',
            'text' => $text,
            'isGroup' => false,
        ], ['x-gateway-token' => config('bot.gateway_token')]);
    }

    public function test_rejects_requests_without_the_shared_secret(): void
    {
        $response = $this->postJson('/api/wa/inbound', [
            'from' => '5215500000000',
            'text' => 'hola',
        ]);

        $response->assertStatus(401);
    }

    public function test_an_inbound_message_makes_the_bot_reply_with_the_plan_list(): void
    {
        Http::fake();

        Plan::create(['nombre' => 'Básico 5GB', 'precio' => 19900, 'activo' => true, 'orden' => 1]);

        $this->inbound('5215500000001', 'hola')->assertOk()->assertJson(['ok' => true]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/send')
                && $request['to'] === '5215500000001'
                && str_contains($request['text'], 'Básico 5GB');
        });

        $this->assertDatabaseHas('bot_contacts', [
            'phone' => '5215500000001',
            'step' => 'choosing',
        ]);
    }

    public function test_a_full_conversation_confirms_the_order_and_assigns_a_number(): void
    {
        Http::fake();

        $plan = Plan::create(['nombre' => 'Ilimitado Social', 'precio' => 29900, 'activo' => true, 'orden' => 1]);
        $numero = Numero::create(['numero' => '+52 55 9999 0001', 'estado' => 'disponible']);

        $from = '5215500000002';

        $this->inbound($from, 'hola')->assertOk();
        $this->inbound($from, '1')->assertOk();
        $this->inbound($from, 'si')->assertOk();

        $contact = BotContact::where('phone', $from)->firstOrFail();
        $this->assertSame('done', $contact->step);

        $pedido = Pedido::where('telefono', $from)->firstOrFail();
        $this->assertSame('numero_asignado', $pedido->estado);
        $this->assertSame($plan->id, $pedido->plan_id);

        $numero->refresh();
        $this->assertSame('asignado', $numero->estado);
        $this->assertSame($pedido->id, $numero->pedido_id);

        $this->assertDatabaseHas('clientes', ['telefono' => $from]);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/send')
            && $request['to'] === $from
            && str_contains($request['text'], $numero->numero));
    }

    public function test_faq_questions_are_answered_without_disturbing_the_funnel(): void
    {
        Http::fake();

        Plan::create(['nombre' => 'Básico 5GB', 'precio' => 19900, 'activo' => true, 'orden' => 1]);

        $from = '5215500000003';

        $this->inbound($from, 'hola')->assertOk();
        $this->inbound($from, '¿tienen cobertura en mi zona?')->assertOk();

        // The FAQ reply must not advance the funnel state.
        $this->assertDatabaseHas('bot_contacts', ['phone' => $from, 'step' => 'choosing']);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/send')
            && str_contains($request['text'], 'cobertura'));
    }
}
