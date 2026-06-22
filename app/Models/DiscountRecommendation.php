<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountRecommendation extends Model
{
    protected $connection = 'pgsql_sales';

    protected $table = 'discount_recommendations';

    const UPDATED_AT = null;

    protected $fillable = [
        'deadstock_analysis_id',
        'discount_percent',
        'recommendation_note',
    ];

    protected $casts = [
        'discount_percent' => 'integer',
    ];

    public function deadstockAnalysis(): BelongsTo
    {
        return $this->belongsTo(DeadstockAnalysis::class);
    }
}
