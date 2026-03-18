<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'company_name',
        'contact_name',
        'number_phone',
        'description'

    ];

    /**
     * Relación inversa muchos a muchos con productos
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('price');
    }

    public function buys(): HasMany
    {
        return $this->hasMany(Buy::class);
    }
}
