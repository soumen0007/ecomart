<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $table = 'shopping_list';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'note',
        'added_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}