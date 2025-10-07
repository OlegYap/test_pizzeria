<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->adminToken = JWTAuth::fromUser($this->admin);

        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        $this->userToken = JWTAuth::fromUser($this->user);
    }

    public function test_show_order(): void
    {
        $order = Order::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->get("/api/admin/orders/{$order->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $order->id);
    }

    public function test_index_orders(): void
    {
        Order::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->get('/api/admin/orders');

        $response->assertStatus(200)->assertJsonCount(Order::count());
    }

    public function test_create_order(): void
    {
        $user = User::factory()->create();

        $payload = [
            'user_id' => $user->id,
            'address' => 'test address',
            'delivery_time' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->userToken])
            ->postJson('/api/user/orders', $payload);

        $response->assertCreated()->assertJsonFragment([
            'address' => 'test address',
            'delivery_time' => $payload['delivery_time'],
        ]);

        $this->assertDatabaseHas('orders', $payload);
    }

    public function test_update_order(): void
    {
        $order = Order::factory()->create();
        $user = User::factory()->create();

        $payload = [
            'user_id' => $user->id,
            'address' => 'test address',
            'delivery_time' => now()->addDay()->format('Y-m-d H:i:s'),
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->putJson("/api/admin/orders/{$order->id}", $payload);

        $response->assertOk()->assertJsonFragment(['address' => 'test address']);

        $this->assertDatabaseHas('orders', $payload);
    }

    public function test_delete_order(): void
    {
        $order = Order::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->userToken])
            ->deleteJson("/api/user/orders/{$order->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_paginate_order(): void
    {
        Order::factory()->count(35)->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->getJson('/api/admin/orders?page=1');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->getJson('/api/admin/orders?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->getJson('/api/admin/orders?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(5, 'data');
    }
}
