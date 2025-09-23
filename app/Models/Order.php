<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'article_count',
        'total_price',
        'slug',
    ];

    // 🔗 Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articles()
    {
        return $this->hasMany(OrderArticle::class);
    }

    public function transaction()

    {
        return $this->hasOne(Transaction::class);
    }
}
