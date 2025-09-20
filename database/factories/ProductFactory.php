<?php

namespace Database\Factories;

use App\Enums\ProductEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(2, 1, 999),
            'type' => $this->faker->randomElement([ProductEnum::cases()]),
        ];
    }
}
