<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property string $price
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @method static \Database\Factories\OrderProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
