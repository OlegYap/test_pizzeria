<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property int $cart_id
 * @property int $quantity
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Database\Factories\CartProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartProduct whereUpdatedAt($value)
 * @property-read \App\Models\Cart $cart
 * @mixin \Eloquent
 */
class CartProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'cart_id',
        'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class,'cart_id','id');
    }
}
