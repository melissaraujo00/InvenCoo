<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;
use NotificationChannels\WhatsApp\Component; // ← Importante

class TransferWhatsappNotification extends Notification
{
    protected $transfer;
    protected $templateName;
    protected $variables;

    public function __construct($transfer, $templateName, $variables)
    {
        $this->transfer = $transfer;
        $this->templateName = $templateName;
        $this->variables = $variables;
    }

    public function via($notifiable)
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable)
    {
        $template = WhatsAppTemplate::create()
            ->name($this->templateName)
            ->to($notifiable->number) // Ajusta el campo según tu modelo
            ->language('es_MX');

        // Cada variable debe ser un objeto Component
        foreach ($this->variables as $variable) {
            $template->body(Component::text((string) $variable));
        }

        return $template;
    }
}
