<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class FileUploaded extends Notification
{
    use Queueable;

    private $document;
    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
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
            ->subject(__('New File Uploaded'))
            ->line(__('A new file has been uploaded by :name', ['name' => $this->document->user->name]))
            ->line(__('File Name: :file_name', ['file_name' => $this->document->file_name]))
            ->line(__('The file is attached to this email.'))
            ->attach(Storage::path($this->document->file_path), [
                'as' => $this->document->file_name,
                'mime' => $this->document->file_mime_type,
            ])
            ->line(__('Thank you!'));
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
