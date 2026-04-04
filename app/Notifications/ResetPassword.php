<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    protected function buildMailMessage($url): \Illuminate\Notifications\Messages\MailMessage
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:4200');
        
        return (new MailMessage)
            ->subject('Reset your password - vonchess')
            ->view('emails.reset-password', [
                'url' => $url,
                'frontendUrl' => $frontendUrl,
            ]);
    }
}
