<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    protected $fillable = [
        'product_id',
        'office_id',
        'date_movement',
        'type_id',
        'user_id',
        'transaction_id',
        'amount',
        'description',
        'stock_total',
        'input_type'
    ];

    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function type():BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
}
