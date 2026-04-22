<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Transfer extends Model
{
    use AuthorizesRequests;
    const COOPERATIVE_ID = 1;
    const RESTAURANT_ID = 2;
    protected $fillable = [
        'originating_branch',
        'destination_branch',
        'requesting_user',
        'user_authorizes',
        'creation_date',
        'shipping_date',
        'receipt_date',
        'status',
        'out_movement_id',
        'in_movement_id'
    ];

    protected $casts = [
    'status' => StatusEnum::class,
    'creation_date' => 'datetime',
    'shipping_date' => 'datetime',
    'receipt_date' => 'datetime',
];

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

    public function outMovement(): BelongsTo
    {
        return $this->belongsTo(Movement::class, 'out_movement_id');
    }

    public function inMovement(): BelongsTo
    {
        return $this->belongsTo(Movement::class, 'in_movement_id');
    }
}
