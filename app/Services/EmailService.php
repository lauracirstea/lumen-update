<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Class EmailService
 *
 * @package App\Services
 */
class EmailService
{
    /**
     * Send code on email for forgot password
     *
     * @param User $user
     */
    public function sendForgotPasswordCode(User $user)
    {
        Mail::send('emails.forgot', ['user' => $user], function ($message) use ($user) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->subject(env('APP_NAME') . ' - Forgot password code');

            $message->to($user->email);
        });
    }
}
