<?php
// app/Notifications/TransferApproved.php

namespace App\Notifications;

use App\Models\Transfer;
use Illuminate\Notifications\Notification;

class TransferApproved extends Notification
{
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
            'title' => 'Transferencia aprobada',
            'message' => "Tu transferencia #{$this->transfer->id} ha sido aprobada. Los productos serán enviados pronto.",
            'transfer_id' => $this->transfer->id,
            'type' => 'transfer_approved',
            'url' => route('transfers.show', $this->transfer),
        ];
    }
}
