<?php
// app/Notifications/TransferApproved.php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\Component;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;


class TransferApproved extends Notification implements ShouldQueue
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
            'title' => 'Transferencia aprobada',
            'message' => "Tu transferencia #{$this->transfer->id} ha sido aprobada. Los productos serán enviados pronto.",
            'transfer_id' => $this->transfer->id,
            'type' => 'transfer_approved',
            'url' => route('transfers.show', $this->transfer, false),
        ];
    }

        public function toWhatsApp($notifiable)
    {
        return WhatsAppTemplate::create()
            ->name('transfer_approved')
            ->to($notifiable->number)
            ->language('es_MX')
            ->body(Component::text((string) $this->transfer->id))
            ->body(Component::text(route('transfers.show', $this->transfer)));
    }
}
