<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$t = App\Models\Transaction::first();
print_r($t ? $t->toArray() : 'No transactions');
