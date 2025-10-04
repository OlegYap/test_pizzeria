<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_cartProducts()
    {
        $cartProduct = CartProduct::factory()->create();

        $response = $this->get("/api/cart-products/{$cartProduct->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $cartProduct->id);
    }

    public function test_index_cartProducts(): void
    {
        CartProduct::factory()->create();

        $response = $this->get('/api/cart-products');

        $response->assertStatus(200)->assertJsonCount(3);
    }

    public function test_create_cartProducts(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create();

        $payload = [
            'product_id' => $product->id,
            'cart_id' => $cart->id,
            'quantity' => 1,
        ];

        $response = $this->postJson('/api/cart-products', $payload);

        $response->assertCreated()->assertJsonFragment($payload);

        $this->assertDatabaseHas('cart_products', $payload);
    }

    public function test_update_cartProducts(): void
    {
        $cartProduct = CartProduct::factory()->create();

        $payload = [
            'product_id' => $cartProduct->product_id,
            'cart_id' => $cartProduct->cart_id,
            'quantity' => $cartProduct->quantity,
        ];

        $response = $this->putJson('/api/cart-products/' . $cartProduct->id, $payload);

        $response->assertOk()->assertJsonFragment($payload);

        $this->assertDatabaseHas('cart_products', $payload);
    }

    public function test_delete_cartProducts(): void
    {
        $cartProduct = CartProduct::factory()->create();

        $response = $this->deleteJson("/api/cart-products/{$cartProduct->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('cart_products', ['id' => $cartProduct->id]);
    }

    public function test_paginate_cartProducts(): void
    {
        CartProduct::factory()->count(35)->create();

        $response = $this->getJson('/api/cart-products?page=1');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->getJson('/api/cart-products?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->getJson('/api/cart-products?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(5, 'data');
    }
}
