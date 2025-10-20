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

class CartTest extends TestCase
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

    public function test_show_carts(): void
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get('/api/user/cart');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $cart->id);
    }


    public function test_create_carts(): void
    {
        $product = Product::factory()->create();

        $this->assertDatabaseMissing('carts', ['user_id' => $this->user->id]);

        $payload = [
            'product_id' => $product->id,
            'quantity' => 1,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/user/cart-products', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.product_id', $product->id)
            ->assertJsonPath('data.quantity', 1)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'product_id',
                    'cart_id',
                    'quantity',
                ],
            ]);

        $this->assertDatabaseHas('carts', ['user_id' => $this->user->id]);

        $cart = Cart::where('user_id', $this->user->id)->first();
        $this->assertDatabaseHas('cart_products', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

    }


    public function test_clear_cart(): void
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();

        CartProduct::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson('api/user/cart/clear');

        $response->assertNoContent();

        $this->assertDatabaseHas('carts', ['id' => $cart->id]);

        $this->assertDatabaseMissing('cart_products', ['cart_id' => $cart->id]);
    }


    public function test_paginate_cart(): void
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartProduct::factory()->count(35)->create(['cart_id' => $cart->id]);

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
