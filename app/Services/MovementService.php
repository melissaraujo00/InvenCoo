
namespace App\Services;

use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\ProductStock; // si llevas stock por oficina

class MovementService
{
    public static function registerFromPurchase($purchase)
    {
        // $purchase es el objeto de la compra recién creada
        $movement = Movement::create([
            'transaction_id' => self::generateTransactionId(),
            'date_movement' => $purchase->purchase_date,
            'office_id' => $purchase->office_id,
            'type_id' => Type::where('name', 'Compra')->first()->id, // o el ID fijo
            'input_type' => 'E', // Entrada
            'user_id' => $purchase->user_id,
            'description' => 'Compra #' . $purchase->id,
            'total_quantity' => $purchase->details->sum('quantity'),
            // otros campos como origin_office_id, destination_office_id se dejan null
        ]);

        foreach ($purchase->details as $detail) {
            // Calcular stock después (necesitas una tabla stock o calcularlo)
            $stockAfter = self::calculateStockAfter($detail->product_id, $movement->office_id, $detail->quantity, 'E');

            MovementDetail::create([
                'movement_id' => $movement->id,
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'unit_price' => $detail->price,
                'subtotal' => $detail->quantity * $detail->price,
                'stock_after' => $stockAfter,
            ]);
        }

        return $movement;
    }

    public static function registerFromTransfer($transfer)
    {
        // Transferencia: Salida de oficina origen (input_type = 'S') y Entrada en oficina destino (input_type = 'E')
        // Se crean dos movimientos (uno de salida, uno de entrada) o un solo movimiento con origen/destino.
        // Depende de tu modelo. Aquí asumimos un movimiento con origen y destino.
        $movement = Movement::create([
            'transaction_id' => self::generateTransactionId(),
            'date_movement' => $transfer->transfer_date,
            'office_id' => $transfer->source_office_id, // oficina que envía
            'origin_office_id' => $transfer->source_office_id,
            'destination_office_id' => $transfer->target_office_id,
            'type_id' => Type::where('name', 'Transferencia')->first()->id,
            'input_type' => 'S', // Salida para la oficina origen
            'user_id' => $transfer->user_id,
            'description' => 'Transferencia a ' . $transfer->targetOffice->name,
            'total_quantity' => $transfer->details->sum('quantity'),
        ]);

        foreach ($transfer->details as $detail) {
            // Stock después en oficina origen (resta)
            $stockAfterSource = self::calculateStockAfter($detail->product_id, $transfer->source_office_id, $detail->quantity, 'S');
            MovementDetail::create([
                'movement_id' => $movement->id,
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'unit_price' => $detail->price,
                'subtotal' => $detail->quantity * $detail->price,
                'stock_after' => $stockAfterSource,
            ]);

            // Además, deberías crear un segundo movimiento para la oficina destino (entrada)
            // O mejor: crear un solo movimiento con input_type = 'T' y manejar ambos stocks.
        }
    }

    private static function generateTransactionId()
    {
        // similar a tu método, pero puede ser estático
        $today = now()->format('Ymd');
        $prefix = "MOV-{$today}-";
        $last = Movement::where('transaction_id', 'LIKE', $prefix . '%')->orderBy('id', 'desc')->first();
        $newNumber = $last ? str_pad((int)substr($last->transaction_id, -4) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        return $prefix . $newNumber;
    }

    private static function calculateStockAfter($productId, $officeId, $quantity, $inputType)
    {
        // Lógica para calcular el stock actual y sumar/restar
        $lastStock = MovementDetail::whereHas('movement', function($q) use ($officeId) {
                $q->where('office_id', $officeId);
            })->where('product_id', $productId)
            ->orderBy('id', 'desc')
            ->value('stock_after') ?? 0;

        return $inputType === 'E' ? $lastStock + $quantity : $lastStock - $quantity;
    }
}
