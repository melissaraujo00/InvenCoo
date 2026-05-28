<?php

namespace App\Http\Requests;

use App\Models\PurchaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        // Reutiliza la misma lógica de autorización que en el método create
        return Gate::allows('create', PurchaseRequest::class);
    }

    /**
     * Reglas de validación aplicables.
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Mensajes personalizados de error (opcional).
     */
    public function messages(): array
    {
        return [
            'products.required' => 'Debe agregar al menos un producto.',
            'products.min' => 'Debe agregar al menos un producto.',
            'products.*.product_id.required' => 'Cada producto es requerido.',
            'products.*.product_id.exists' => 'Uno de los productos seleccionados no es válido.',
            'products.*.quantity.required' => 'La cantidad es obligatoria.',
            'products.*.quantity.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}
