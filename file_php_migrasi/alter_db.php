<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::connection('pgsql_sales')->statement("ALTER TABLE transactions ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'unpaid'");
DB::connection('pgsql_sales')->statement("ALTER TABLE transactions ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'pending'");
DB::connection('pgsql_sales')->statement("UPDATE transactions SET payment_method = 'cash', status = 'lunas'");

echo "Berhasil update tabel transactions!\n";
