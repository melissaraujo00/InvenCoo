<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    protected $fillable = [
        'originating_branch',
        'destination_branch',
        'requesting_user',
        'user_authorizes',
        'creation_date',
        'shipping_date',
        'receipt_date',
        'status'
    ];

    protected $casts = ['status' => StatusEnum::class];

    public function originatingBranch():BelongsTo
    {
        return $this->belongsTo(Office::class, 'originating_branch');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'destination_branch');
    }

    public function requestingUser() : BelongsTo
    {
        return $this->belongsTo(User::class, 'requesting_user');
    }

    public function authorizingUser() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_authorizes');
    }

    public function details():HasMany
    {
        return $this->hasMany(TransferDetail::class);
    }
}
