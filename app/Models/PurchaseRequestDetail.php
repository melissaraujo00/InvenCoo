<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'product_id',
        'quantity',
    ];

    // Relación inversa con la solicitud de compra
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    // Relación con el producto solicitado
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
