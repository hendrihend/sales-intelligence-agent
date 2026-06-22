<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesUser extends Model
{
    protected $connection = 'pgsql_sales';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];
}
