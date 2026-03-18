<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'price',
        'subtotal',
        'quantity',
        'product_id',
        'buy_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function buy(): BelongsTo
    {
        return $this->belongsTo(Buy::class);
    }

}
