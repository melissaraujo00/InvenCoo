<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\Component;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;

class PurchaseRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $purchase;

    public function __construct(PurchaseRequest $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        // Si el usuario tiene número de teléfono, activamos WhatsApp automáticamente
        if (!empty($notifiable->number)) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase( $notifiable): array
    {
         return [
            'title' => 'Rechazo de compra',
            'message' => "La solicitud de la compra #{$this->purchase->id} ha sido rechazada.",
            'purchase_request_id' => $this->purchase->id,
            'type' => 'purchase_request',
            'url' => route('purchases.show', $this->purchase),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toWhatsApp( $notifiable)
    {
         return WhatsAppTemplate::create()
            ->name('purchase_rejected')
            ->to($notifiable->number)
            ->language('es_MX')
            ->body(Component::text((string) $this->purchase->id));
    }
}
