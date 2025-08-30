<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    protected $table = 'cart_products';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity'
    ];
}
