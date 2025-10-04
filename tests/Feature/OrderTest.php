<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_order(): void
    {
        $order = Order::factory()->create();

        $response = $this->get("/api/orders/$order->id");

        $response->assertStatus(200)->assertJsonPath('data.id', $order->id);
    }

    public function test_index_orders(): void
    {
        Order::factory()->create();

        $response = $this->get('/api/orders');

        $response->assertStatus(200)->assertJsonCount(3);
    }

    public function test_create_order(): void
    {
        $user = User::factory()->create();

        $payload = [
            'user_id' => $user->id,
            'address' => 'test address',
            'delivery_time' => 'test delivery address',
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertCreated()->assertJsonFragment($payload);

        $this->assertDatabaseHas('orders', $payload);
    }

    public function test_update_order(): void
    {
        $order = Order::factory()->create();
        $user = User::factory()->create();

        $payload = [
            'user_id' => $user->id,
            'address' => 'test address',
            'delivery_time' => 'test delivery address',
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $payload);

        $response->assertOk()->assertJsonFragment(['address' => 'test address']);

        $this->assertDatabaseHas('orders', $payload);
    }

    public function test_delete_order(): void
    {
        $order = Order::factory()->create();

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_paginate_order(): void
    {
        Order::factory()->count(35)->create();

        $response = $this->getJson('/api/orders?page=1');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->getJson('/api/orders?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->getJson('/api/orders?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(5, 'data');

    }
}
