<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'contact_number',
        'email',
        'username',
        'password'
    ];
}
