<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$hashed = bcrypt('password');
Illuminate\Support\Facades\DB::connection('pgsql_sales')->table('users')->update(['password' => $hashed]);
echo "Passwords updated.";
