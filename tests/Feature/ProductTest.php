<?php

namespace Tests\Feature;

use App\Enums\ProductEnum;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_show_products(): void
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
        $response->assertStatus(200)->assertJsonCount(3);
    }

    public function test_create_products(): void
    {
        $payload = [
            'name' => 'Test Product',
            'description' => 'Test Product',
            'price' => 100,
            'type' => ProductEnum::Pizza,
        ];

        $response = $this->postJson('api/products', $payload);

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

        $response = $this->postJson('api/products', $payload);
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

        $response = $this->putJson("api/products/{$product->id}", $payload);

        $response->assertOk()->assertJsonFragment(['name' => 'Updated Product']);

        $this->assertDatabaseHas('products', $payload);
    }


    public function test_delete_products(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_paginate_products(): void
    {
        Product::factory()->count(35)->create();

        $response = $this->getJson('/api/products?page=1');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);

        $responsePage2 = $this->getJson('/api/products?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertJsonCount(15, 'data');

        $responsePage3 = $this->getJson('/api/products?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertJsonCount(5, 'data');

    }
}
