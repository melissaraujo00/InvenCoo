<?php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue; // Estricto para segundo plano
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;
use NotificationChannels\WhatsApp\Component;


class TransferRequested extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transfer;

    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    // El cerebro del canal: decide dinámicamente a dónde va el mensaje
    public function via($notifiable): array
    {
        $channels = ['database'];

        // Si el usuario tiene número de teléfono, activamos WhatsApp automáticamente
        if (!empty($notifiable->number)) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    // Canal 1: Base de datos interna
    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Nueva transferencia solicitada',
            'message' => "Se ha solicitado la transferencia #{$this->transfer->id} hacia la sucursal de destino.",
            'transfer_id' => $this->transfer->id,
            'type' => 'transfer_request',
            'url' => route('transfers.show', $this->transfer, false),
        ];
    }

    // Canal 2: WhatsApp (Meta API)
    public function toWhatsApp($notifiable)
    {
        return WhatsAppTemplate::create()
            ->name('transfer_request_admin')
            ->to($notifiable->number)
            ->language('es_MX')
            ->body(Component::text((string) $this->transfer->id))
            ->body(Component::text(route('transfers.show', $this->transfer)));
    }
}
