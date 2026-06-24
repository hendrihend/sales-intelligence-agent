<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$hashed = bcrypt('password');
$exists = Illuminate\Support\Facades\DB::connection('pgsql_sales')->table('users')->where('email', 'guest@gmail.com')->exists();

if (!$exists) {
    Illuminate\Support\Facades\DB::connection('pgsql_sales')->table('users')->insert([
        'name' => 'Guest User',
        'email' => 'guest@gmail.com',
        'password' => $hashed,
        'role' => 'guest',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Guest user created.";
} else {
    echo "Guest user already exists.";
}
