<?php

namespace App\Rules;

use App\Enums\ProductEnum;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductLimitRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $product = Product::find($value);

        if (!$product) {
            return;
        }

        $typeEnum = ProductEnum::from($product->type);

        $limit = match ($typeEnum) {
            ProductEnum::Pizza => 10,
            ProductEnum::Drink => 20,
        };

        $count = \DB::table('order_products')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->where('order_products.order_id', request('order_id'))
            ->where('products.type', $product->type)
            ->sum('order_products.quantity');

        if ($count + request('quantity') > $limit) {
            $fail("Вы не можете добавить больше {$limit} {$typeEnum->label()}");
        }
    }
}
