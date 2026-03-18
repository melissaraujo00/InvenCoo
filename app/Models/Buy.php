<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buy extends Model
{
    protected $fillable = [
        'total',
        'date',
        'subtotal',
        'discount',
        'user_id',
        'office_id',
        'total_iva',
        'supplier_id',
        'is_cancelled'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details():HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

}
