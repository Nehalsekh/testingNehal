<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordReset extends Notification
{
    //todo implement ShouldQueue
    use Queueable;

    //no of tries before adding to job fail table
    //public $tries = 5;

    public $data;
    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data=$data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Reset')
            ->greeting('Hello '.$this->data['name'])
            ->line('Your password has been successfully reset.')
            ->action('Notification Action', route('login'))
            ->line('Thank you for using our application!');

        // Use with Blade
//        return (new MailMessage)->view(
//            'mail.password-reset', ['data' => $this->data]
//        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
//            'model' => 'Task',
//            'text' => ('Approved task #' . $this->task->id . ' ' . $this->task->identifier),
//            'url' => $this->task->getResourceUrl('show', false),
//            'color_id' => Color::SUCCESS,
        ];
    }
}
