<?php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\Component;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;

class TransferShipped extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transfer;

    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        if (!empty($notifiable->number)) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Transferencia enviada',
            'message' => "Los productos de la transferencia #{$this->transfer->id} ya van en camino a tu sucursal.",
            'transfer_id' => $this->transfer->id,
            'type' => 'transfer_shipped',
            'url' => route('transfers.show', $this->transfer),
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return WhatsAppTemplate::create()
            ->name('transfer_shipped_admin') // <-- CAMBIA ESTO por el nombre real de tu plantilla en Meta
            ->to($notifiable->number)
            ->language('es_MX')
            ->body(Component::text((string) $this->transfer->id))
            ->body(Component::text(route('transfers.show', $this->transfer)));
    }
}
