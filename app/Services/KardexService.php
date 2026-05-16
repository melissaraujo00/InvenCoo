<?php
namespace App\Services;

use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Type;

class KardexService
{
    public function createMovement(array $data): Movement
    {
        $typeId = Type::firstOrCreate(
            ['name' => $data['type_name']],
            ['description' => $data['type_description'] ?? $data['type_name']]
        )->id;

        return Movement::create([
            'office_id'      => $data['office_id'],
            'date_movement'  => $data['date'],
            'type_id'        => $typeId,
            'user_id'        => $data['user_id'],
            'transaction_id' => $data['transaction_id'],
            'description'    => $data['description'],
            'input_type'     => $data['input_type'],
            'origin_office_id'      => $data['origin_office_id'] ?? null,
            'destination_office_id' => $data['destination_office_id'] ?? null,
        ]);
    }

    public function addDetail(Movement $movement, array $data): void
    {
        MovementDetail::create([
            'movement_id' => $movement->id,
            'product_id'  => $data['product_id'],
            'quantity'    => $data['quantity'],
            'unit_price'  => $data['unit_price'],
            'subtotal'    => $data['unit_price'] * $data['quantity'],
            'stock_after' => $data['stock_after'],
        ]);
    }
}
