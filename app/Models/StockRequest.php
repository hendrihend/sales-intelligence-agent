<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRequest extends Model
{
    protected $connection = 'pgsql_sales';

    protected $table = 'stock_requests';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'requested_by',
        'approved_by',
        'qty',
        'status',
        'request_date',
        'approved_date',
    ];

    protected $casts = [
        'qty' => 'integer',
        'request_date' => 'datetime',
        'approved_date' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
