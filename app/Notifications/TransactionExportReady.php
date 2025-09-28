<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionExportReady extends Notification implements ShouldQueue
{
    use Queueable;

    // public string $filePath;
    public string $fileUrl;
    /**
     * Create a new notification instance.
     */
    public function __construct(string $fileUrl)
    {
        //
        $this->fileUrl = $fileUrl;
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
            ->subject('Your Transaction Export is Ready')
            ->greeting("Hello {$notifiable->first_name},")
            ->line('Your transaction history export has been completed successfully.')
            // ->line("File path: {$this->filePath}")
            // ->action('Download Export', url("/storage/{$this->filePath}"))
            ->action('Download Export', $this->fileUrl)
            ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
