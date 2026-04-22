<?php
// app/Notifications/TransferRequested.php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransferRequested extends Notification
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
            'title' => 'Nueva transferencia solicitada',
            'message' => "ha solicitado una transferencia desde {$this->transfer->originatingBranch->name} hacia {$this->transfer->destinationBranch->name}",
            'transfer_id' => $this->transfer->id,
            'type' => 'transfer_request',
            'url' => route('transfers.show', $this->transfer),
        ];
    }
}
