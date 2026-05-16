<?php

namespace App\Services;

use App\Models\Product;

class StockService
{
    /**
     * Incrementa el stock de un producto con bloqueo pesimista.
     * Devuelve el producto ya actualizado para poder leer stock_after.
     */
    public function increment(int $productId, int $quantity): Product
    {
        $product = Product::lockForUpdate()->findOrFail($productId);
        $product->increment('stock', $quantity);

        return $product;
    }

    /**
     * Decrementa el stock de un producto con bloqueo pesimista.
     * Lanza excepción si no hay stock suficiente para evitar negativos.
     */
    public function decrement(int $productId, int $quantity): Product
    {
        $product = Product::lockForUpdate()->findOrFail($productId);

        if ($product->stock < $quantity) {
            throw new \RuntimeException(
                "Stock insuficiente para '{$product->name}'. "
                . "Disponible: {$product->stock}, solicitado: {$quantity}."
            );
        }

        $product->decrement('stock', $quantity);

        return $product;
    }

    /**
     * Verifica si hay stock suficiente sin modificarlo.
     * Útil para validaciones previas a una transacción.
     */
    public function hasSufficient(int $productId, int $quantity): bool
    {
        $product = Product::find($productId);

        return $product && $product->stock >= $quantity;
    }
}
