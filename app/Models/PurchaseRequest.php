<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requesting_user_id',
        'authorizing_user_id',
        'status',
        'note',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    // Relación con el usuario que solicita
    public function requestingUser()
    {
        return $this->belongsTo(User::class, 'requesting_user_id');
    }

    // Relación con el usuario que autoriza (puede ser nulo)
    public function authorizingUser()
    {
        return $this->belongsTo(User::class, 'authorizing_user_id');
    }

    // Detalles de la solicitud (productos)
    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class);
    }
}
