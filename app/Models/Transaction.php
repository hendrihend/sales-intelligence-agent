<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $connection = 'pgsql_sales';

    protected $table = 'transactions';

    public $timestamps = false;

    protected $fillable = [
        'cashier_id',
        'transaction_date',
        'total',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    protected $appends = ['formatted_id'];

    public function getFormattedIdAttribute()
    {
        return 'TRX-IN' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(SalesUser::class, 'cashier_id');
    }
}
