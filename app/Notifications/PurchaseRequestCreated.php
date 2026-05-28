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

class PurchaseRequestCreated extends Notification  implements ShouldQueue
{
    use Queueable;

    protected $purchase;

    public function __construct(PurchaseRequest $purchase)
    {
        $this->purchase = $purchase;
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
            'title' => 'Nueva Compra solicitada',
            'message' => "Se ha solicitado la compra #{$this->purchase->id} hacia la sucursal de destino.",
            'purchase_request_id' => $this->purchase->id,
            'type' => 'purchase_request',
            'url' => route('purchases.show', $this->purchase),
        ];
    }

    // Canal 2: WhatsApp (Meta API)
    public function toWhatsApp($notifiable)
    {
        return WhatsAppTemplate::create()
            ->name('purchase_request_admin')
            ->to($notifiable->number)
            ->language('es_MX')
            ->body(Component::text((string) $this->purchase->id));
    }
}
