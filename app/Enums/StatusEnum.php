<?php

namespace App\Enums;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case PARTIALLY_APPROVED = 'partially_approved';
    case REJECTED = 'rejected';
    case SHIPPED = 'shipped';
    case RECEIVED = 'received';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::APPROVED => 'Aprobada',
            self::PARTIALLY_APPROVED => 'Aprobación parcial',
            self::REJECTED => 'Rechazada',
            self::SHIPPED => 'Enviada',
            self::RECEIVED => 'Recibida',
        };
    }

    // Opcional: obtener todos los valores para usarlos en validación
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
