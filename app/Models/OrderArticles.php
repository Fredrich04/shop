<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderArticles extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'slug',
        'quantity',
        'price',
    ];

    // 🔗 Relations
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
