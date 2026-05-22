<?php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\Component;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;

class TransferReadyToShip extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transfer;

    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    public function via($notifiable)
    {
        $channels = ['database', 'broadcast'];
         if (!empty($notifiable->number)) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toArray($notifiable)
    {
        return [
            'transfer_id' => $this->transfer->id,
            'message' => '📦 Transferencia #' . $this->transfer->id . ' lista para enviar',
            'url' => route('transfers.show', $this->transfer),
            'destination' => $this->transfer->destinationBranch->name,
        ];
    }

        public function toWhatsApp($notifiable)
    {
        return WhatsAppTemplate::create()
            ->name('transfer_ready_for_ship')
            ->to($notifiable->number)
            ->language('es_MX')
            ->body(Component::text((string) $this->transfer->id))
            ->body(Component::text(route('transfers.show', $this->transfer)));
    }
}
