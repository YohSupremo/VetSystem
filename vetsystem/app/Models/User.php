<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'last_name',
        'first_name',
        'age',
        'contact_number',
        'username',
        'password'
    ];
}
