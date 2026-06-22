<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$tables = Illuminate\Support\Facades\DB::connection('pgsql_sales')->select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'");
print_r($tables);
