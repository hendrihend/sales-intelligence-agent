<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    Illuminate\Support\Facades\DB::connection('pgsql_sales')->statement("ALTER TYPE user_role ADD VALUE 'guest'");
    echo "Enum updated.\n";
} catch (\Exception $e) {
    echo "Enum update failed or already exists: " . $e->getMessage() . "\n";
}

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
    echo "Guest user created.\n";
} else {
    echo "Guest user already exists.\n";
}
