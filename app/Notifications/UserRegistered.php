<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification
{
    use Queueable;

    protected $email;
    protected $password;

    /**
     * Create a new notification instance.
     *
     * @param string $email
     * @param string $password
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(__('Your Account Information'))
                    ->greeting(__('Hello!'))
                    ->line(__('Your account has been created successfully.'))
                    ->line(__('You can log in using the following credentials:'))
                    ->line(__('URL: ') . url('/login'))
                    ->line(__('Email: ') . $this->email)
                    ->line(__('Password: ') . $this->password)
                    ->action(__('Login'), url('/login'))
                    ->line(__('Please change your password after logging in for the first time.'))
                    ->salutation(__('Regards,') . "\n" . config('app.name'));
    }
}
