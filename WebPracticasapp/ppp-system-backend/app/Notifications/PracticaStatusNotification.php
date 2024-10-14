<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PracticaStatusNotification extends Notification
{
    use Queueable;

    public $status;
    public $practica;

    /**
     * Create a new notification instance.
     */
    public function __construct($status, $practica)
    {
        $this->status = $status;
        $this->practica = $practica;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting('Hola ' . $notifiable->name)
                    ->line('La práctica "' . $this->practica->empresa . '" ha sido ' . $this->status . '.')
                    ->action('Ver Detalles', url('/practicas/' . $this->practica->id))
                    ->line('Gracias por usar nuestro sistema!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'status' => $this->status,
            'practica_id' => $this->practica->id,
            'empresa' => $this->practica->empresa,
        ];
    }
}
