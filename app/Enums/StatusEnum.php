<?php

namespace App\Enums;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case SHIPPED = 'shipped';
    case IN_TRANSIT = 'in_transit';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PREPARING => 'En preparación',
            self::SHIPPED => 'Enviada',
            self::IN_TRANSIT => 'En tránsito',
            self::COMPLETED => 'Completada',
            self::CANCELLED => 'Cancelada',
            self::REJECTED => 'Rechazada',
        };
    }

    // Opcional: obtener todos los valores para usarlos en validación
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
