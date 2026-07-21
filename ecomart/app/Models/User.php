<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password_hash',
        'phone'
    ];

    protected $hidden = [
        'password_hash'
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function shoppingList()
    {
        return $this->hasMany(ShoppingList::class);
    }
}