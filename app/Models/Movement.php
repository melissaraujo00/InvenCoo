<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movement extends Model
{
    protected $fillable = [
        'office_id',
        'date_movement',
        'type_id',
        'user_id',
        'transaction_id',
        'description',
        'input_type',
        'origin_office_id',
        'destination_office_id'
    ];



    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function type():BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function originatingBranch():BelongsTo
    {
        return $this->belongsTo(Office::class, 'origin_office_id');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'destination_office_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(MovementDetail::class);
    }


}
