<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_create_cartProducts_creates_cart_automatically(): void
    {

        $this->assertDatabaseMissing('carts',['user_id' => $this->user->id]);

        $product = Product::factory()->create();

        $payload = [
            'product_id' => $product->id,
            'quantity' => 2,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/user/cart-products', $payload);

        $response->assertCreated()->assertJsonFragment(
            [
                'product_id' => $product->id,
                'quantity' => 2,
            ]
        );

        $this->assertDatabaseHas('carts', ['user_id' => $this->user->id]);

        $this->assertDatabaseHas('cart_products', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    public function test_show_cartProducts()
    {
        $cartProduct = CartProduct::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get("/api/user/cart-products/{$cartProduct->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $cartProduct->id);
    }

    public function test_index_cartProducts(): void
    {
        CartProduct::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get('/api/user/cart-products');

        $response->assertStatus(200)->assertJsonCount(CartProduct::count());
    }

    public function test_create_cartProducts(): void
    {
        $product = Product::factory()->create();

        $payload = [
            'product_id' => $product->id,
            'quantity' => 1,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/user/cart-products', $payload);

        $response->assertCreated()->assertJsonFragment($payload);

        $this->assertDatabaseHas('carts', ['user_id' => $this->user->id]);

        $this->assertDatabaseHas('cart_products', $payload);
    }

    public function test_update_cartProducts(): void
    {
        $cartProduct = CartProduct::factory()->create();

        $payload = [
            'product_id' => $cartProduct->product_id,
            'cart_id' => $cartProduct->cart_id,
            'quantity' => 2,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson('/api/user/cart-products/' . $cartProduct->id, $payload);

        $response->assertOk()->assertJsonFragment($payload);

        $this->assertDatabaseHas('cart_products', $payload);
    }

    public function test_delete_cartProducts(): void
    {
        $cartProduct = CartProduct::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson("/api/user/cart-products/{$cartProduct->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('cart_products', ['id' => $cartProduct->id]);
    }

    public function test_paginate_cartProducts(): void
    {
        CartProduct::factory()->count(35)->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/user/cart-products?page=1');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/user/cart-products?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/user/cart-products?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(5, 'data');
    }
}
