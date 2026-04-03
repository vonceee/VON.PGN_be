<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail
{
    protected function buildMailMessage($url): \Illuminate\Notifications\Messages\MailMessage
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:4200');

        return (new MailMessage)
            ->subject('Verify your email - vonchess')
            ->view('emails.verify', [
                'url' => $url,
                'frontendUrl' => $frontendUrl,
            ]);
    }
}
