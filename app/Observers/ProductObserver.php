<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;

class ProductObserver
{
    public function updated(Product $product): void
    {
        // 1. Verificamos si la columna 'stock' fue modificada en esta transacción
        if ($product->isDirty('stock')) {

            $oldStock = $product->getOriginal('stock');
            $newStock = $product->stock;
            $minStock = $product->stock_minimun; // Cambia esto si tu columna se llama diferente

            // 2. La regla Anti-Spam: Solo avisa si acaba de cruzar el umbral hacia abajo
            if ($oldStock > $minStock && $newStock <= $minStock) {

                // 3. Buscar a los interesados (Admin y Bodega central)
                $usersToNotify = User::role(['Administrador', 'Administrador Restaurante'])->get();

                if ($usersToNotify->isNotEmpty()) {
                    Notification::send($usersToNotify, new LowStockNotification($product));
                }
            }
        }
    }
}
