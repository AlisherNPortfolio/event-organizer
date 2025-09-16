<?php

namespace App\Infrastructure\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends Notification
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage())
            ->subject(__('auth.mail.verify.subject'))
            ->line(__('auth.mail.verify.line1', [
                'count' => config('auth.verification.expire', 60),
            ]))
            ->action(__('auth.mail.verify.action'), $url)
            ->line(__('auth.mail.verify.line2'));
    }
}
