<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['title', 'price', 'description', 'shortDescription', 'image', 'color', 'category', 'size'];
    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}