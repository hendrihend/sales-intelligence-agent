<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$users = Illuminate\Support\Facades\DB::connection('pgsql_sales')->table('users')->get();
print_r($users);
