<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Illuminate\Support\Facades\DB::connection('pgsql_sales')->statement("ALTER TABLE transactions ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
echo "Success";
