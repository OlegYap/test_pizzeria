<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'address' => $this->faker->address(),
            'delivery_time' => $this->faker->word(),

            'user_id' => User::factory(),
        ];
    }
}
