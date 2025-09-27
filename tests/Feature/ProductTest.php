<?php

namespace Tests\Feature;

use App\Enums\ProductEnum;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_show_products(): void
    {
        $product = Product::factory()->create();

        $response = $this->get("/api/products/{$product->id}");

        $response->assertStatus(200);

    }

    public function test_index_products(): void
    {
        $response = $this->get('/api/products');
        $response->assertStatus(200);
    }

    public function test_create_products(): void
    {
        $payload = [
            'name' => 'Test Product',
            'description' => 'Test Product',
            'price' => 100,
            'type' => ProductEnum::Pizza
        ];

        $response = $this->postJson('api/products', $payload);

        $response->assertCreated()->assertJsonFragment($payload);

        $this->assertDatabaseHas('products', $payload);
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

        $this->assertDatabaseMissing('products',['id' => $product->id]);
    }
}
