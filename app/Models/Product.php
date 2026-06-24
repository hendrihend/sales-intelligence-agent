<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $connection = 'pgsql_sales';

    protected $table = 'products';

    protected $fillable = [
        'name',
        'category',
        'purchase_price',
        'price',
        'stock',
        'status',
        'image',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function deadstockAnalyses(): HasMany
    {
        return $this->hasMany(DeadstockAnalysis::class);
    }

    public function stockRequests(): HasMany
    {
        return $this->hasMany(StockRequest::class);
    }
}
