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
        'stock_minimun',
        'unit_id',
        'stock',
        'office_id',


    ];

    // Proveedores (muchos a muchos con campo price)
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
                    ->withPivot('price')
                    ->withTimestamps();
    }

    /**
     * Relación con categoría (si existe)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }



    /**
     * Relación con marca (si existe)
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function details():HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function transferDetails():HasMany
    {
        return $this->hasMany(TransferDetail::class);
    }

    public function movementDetails(): HasMany
    {
        return $this->hasMany(MovementDetail::class);
    }

    public function office():BelongsTo{
        return $this->belongsTo(Office::class);
    }

    public function unit() : BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

}

