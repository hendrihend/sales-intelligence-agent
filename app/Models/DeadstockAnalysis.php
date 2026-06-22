<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeadstockAnalysis extends Model
{
    protected $connection = 'pgsql_sales';

    protected $table = 'deadstock_analyses';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'last_sale_date',
        'total_sales',
        'stock_remaining',
        'status',
        'analysis_date',
    ];

    protected $casts = [
        'total_sales' => 'integer',
        'stock_remaining' => 'integer',
        'last_sale_date' => 'datetime',
        'analysis_date' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function discountRecommendations(): HasMany
    {
        return $this->hasMany(DiscountRecommendation::class);
    }
}
