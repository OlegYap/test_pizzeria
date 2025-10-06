<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_carts(): void
    {
        $cart = Cart::factory()->create();

        $response = $this->get("api/carts/{$cart->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $cart->id);
    }

    public function test_index_cart(): void
    {
        Cart::factory()->create();

        $response = $this->get("api/carts");

        $response->assertStatus(200)->assertJsonCount(Cart::count());
    }

    public function test_create_carts(): void
    {
        $user = User::factory()->create();
        $payload = [
            'user_id' => $user->id,
        ];

        $response = $this->postJson('api/carts', $payload);

        $response->assertCreated()->assertJsonFragment($payload);

        $this->assertDatabaseHas('carts', $payload);
    }

    public function test_update_carts(): void
    {
        $cart = Cart::factory()->create();

        $user  = User::factory()->create();

        $payload = [
            'user_id' => $user->id,
        ];

        $response = $this->putJson("api/carts/{$cart->id}", $payload);

        $response->assertStatus(200)->assertJsonFragment($payload);

        $this->assertDatabaseHas('carts', $payload);
    }

    public function test_delete_cart(): void
    {
        $cart = Cart::factory()->create();

        $response = $this->deleteJson("api/carts/{$cart->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }


    public function test_paginate_cart(): void
    {
        Cart::factory()->count(35)->create();

        $response = $this->getJson('/api/carts?page=1');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->getJson('/api/carts?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->getJson('/api/carts?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(5, 'data');

    }
}
