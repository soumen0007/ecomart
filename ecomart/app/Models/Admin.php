<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password_hash',
        'full_name',
        'role',
        'created_at',
    ];

    protected $hidden = [
        'password_hash',
    ];
}