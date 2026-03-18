<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'brand_id',
        'office_id',
        'stock',
        'stock_minimun',
        'unit'
    ];

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
                    ->withPivot('price');
    }

    /**
     * Relación con categoría (si existe)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function office():BelongsTo{
        return $this->belongsTo(Office::class);
    }

    /**
     * Relación con marca (si existe)
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function movements():HasMany
    {
        return $this->hasMany(Movement::class);
    }
}

