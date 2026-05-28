<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'number',
        'office_id',
        'status',
    ];




    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function office():BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function movementes():HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function buys(): HasMany
    {
        return $this->hasMany(Buy::class);
    }

    public function requestedTransfers():HasMany
    {
        return $this->hasMany(Transfer::class, 'requesting_user');
    }

    public function authorizedTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'user_authorizes');
    }

    // Solicitudes de compra creadas por este usuario
    public function purchaseRequestsCreated()
    {
        return $this->hasMany(PurchaseRequest::class, 'requesting_user_id');
    }

    // Solicitudes de compra autorizadas por este usuario
    public function purchaseRequestsAuthorized()
    {
        return $this->hasMany(PurchaseRequest::class, 'authorizing_user_id');
    }
}
