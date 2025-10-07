<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderProductTest extends TestCase
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

    public function test_show_order_product(): void
    {
        $orderProducts = OrderProduct::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->get("/api/admin/order-products/{$orderProducts->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $orderProducts->id);
    }

    public function test_index_order_products(): void
    {
        OrderProduct::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->get('/api/admin/order-products');

        $response->assertStatus(200)->assertJsonCount(OrderProduct::count());
    }

    public function test_create_order_product(): void
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $payload = [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 4,
            'price' => 400,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->userToken])
            ->postJson('/api/user/order-products', $payload);

        $response->assertCreated()->assertJsonFragment($payload);

        $this->assertDatabaseHas('order_products', $payload);
    }

    public function test_update_order_product(): void
    {
        $orderProduct = OrderProduct::factory()->create();
        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $payload = [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 900,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->putJson("/api/admin/order-products/{$orderProduct->id}", $payload);

        $response->assertStatus(200)->assertJsonFragment($payload);

        $this->assertDatabaseHas('order_products', $payload);
    }

    public function test_validation_order_product(): void
    {
        $payload = [
            'order_id' => null,
            'product_id' => null,
            'quantity' => null,
            'price' => null,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->userToken])
            ->postJson('/api/user/order-products', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_id', 'product_id', 'quantity', 'price']);
    }

    public function test_delete_order_product(): void
    {
        $orderProduct = OrderProduct::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->deleteJson("/api/admin/order-products/{$orderProduct->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('order_products', $orderProduct->toArray());
    }

    public function test_paginate_order_products(): void
    {
        OrderProduct::factory()->count(35)->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->getJson('/api/admin/order-products?page=1');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->getJson('/api/admin/order-products?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->withHeaders(['Authorization' => 'Bearer ' . $this->adminToken])
            ->getJson('/api/admin/order-products?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(5, 'data');
    }
}
