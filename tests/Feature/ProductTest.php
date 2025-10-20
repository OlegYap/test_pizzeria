<?php

namespace Tests\Feature;

use App\Enums\ProductEnum;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_show_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->get("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', fn($name) => ! empty($name));
    }

    public function test_index_products(): void
    {
        Product::factory()->create();

        $response = $this->get('/api/products');

        $response->assertStatus(200)->assertJsonCount(Product::count(), 'data');
    }

    public function test_create_products(): void
    {
        $payload = [
            'name' => 'Test Product',
            'description' => 'Test Product',
            'price' => 100,
            'type' => ProductEnum::Pizza,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/admin/products', $payload);

        $response->assertCreated()->assertJsonFragment($payload);

        $this->assertDatabaseHas('products', $payload);
    }

    public function test_validation_products(): void
    {
        $payload = [
            'name' => '',
            'description' => 'Unvalid Product',
            'price' => 100,
            'type' => ProductEnum::Pizza,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/admin/products', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_update_products(): void
    {
        $product = Product::factory()->create();

        $payload = [
            'name' => 'Updated Product',
            'type' => $product->type->value,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson("/api/admin/products/{$product->id}", $payload);

        $response->assertOk()->assertJsonFragment(['name' => 'Updated Product']);

        $this->assertDatabaseHas('products', $payload);
    }

    public function test_delete_products(): void
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson("/api/admin/products/{$product->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_paginate_products(): void
    {
        Product::factory()->count(35)->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/admin/products?page=1');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/admin/products?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/admin/products?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(5, 'data');
    }
}
