<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class ActivateAccountOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Activate Your Account')
            ->greeting('Hello!')
            ->line('You have successfully registered on our platform.')
            ->line('Your verification code is:')
            ->line('**' . $this->otp . '**')
            ->line('This code will expire in 10 minutes.')
            ->line('Thank you for using our platform!');
    }
}
