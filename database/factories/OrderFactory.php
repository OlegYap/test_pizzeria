<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
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
            'delivery_time' => $this->faker->dateTimeBetween('now', '+3 days')->format('Y-m-d H:i:s'),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(StatusEnum::cases())?->value,
        ];
    }
}
