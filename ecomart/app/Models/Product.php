<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'category_id',
        'price',
        'image',
        'description',
        'stock',
        'is_organic',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_organic' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shoppingLists()
    {
        return $this->hasMany(ShoppingList::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('assets/images/products/' . $this->image);
    }
}