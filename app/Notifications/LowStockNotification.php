<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\Component;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
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
            'title' => 'Alerta de Stock Mínimo',
            'message' => "El producto {$this->product->name} ha alcanzado su nivel mínimo ({$this->product->stock} unidades restantes).",
            'product_id' => $this->product->id,
            'type' => 'low_stock',
            'url' => route('products.index', $this->product, false), // Ajusta la ruta a tu catálogo
        ];
    }

    public function toWhatsApp($notifiable)
    {
        // Asegúrate de tener una plantilla aprobada en Meta para esto
        return WhatsAppTemplate::create()
            ->name('alerta_stock_minimo')
            ->to($notifiable->number)
            ->language('es_MX')
            ->body(Component::text($this->product->name))
            ->body(Component::text((string)$this->product->stock));
    }
}
