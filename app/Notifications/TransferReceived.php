<?php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransferReceived extends Notification
{
    use Queueable;

    protected $transfer;

    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Transferencia recibida',
            'message' => "La transferencia #{$this->transfer->id} ha sido recibida por el restaurante.",
            'transfer_id' => $this->transfer->id,
            'type' => 'transfer_received',
            'url' => route('transfers.show', $this->transfer),
        ];
    }
}
