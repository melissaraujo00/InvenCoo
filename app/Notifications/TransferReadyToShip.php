<?php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransferReadyToShip extends Notification
{
    use Queueable;

    protected $transfer;

    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; 
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
}
