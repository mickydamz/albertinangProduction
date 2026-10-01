<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification using our branded view.
     */
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);

        $count = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->view('emails.reset-password', [
                'url'   => $url,
                'count' => $count,
            ]);
    }

    /**
     * Get the reset URL (same logic Laravel uses by default).
     */
    protected function resetUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}