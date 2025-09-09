<?php

namespace Database\Seeders;

use App\Models\CartProduct;
use Illuminate\Database\Seeder;

class CartProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CartProduct::factory()->count(50)->create();
    }
}
