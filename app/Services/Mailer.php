<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Plain-text email sender (replaces com.fd.util.EmailUtil).
 * SMTP settings come from the MAIL_* values in .env.
 */
class Mailer
{
    /**
     * @throws \Throwable when the email cannot be sent
     */
    public static function send(string $to, string $subject, string $body): void
    {
        try {
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            Log::info("Email sent to {$to}: {$subject}");
        } catch (\Throwable $e) {
            Log::error("Failed to send email to {$to}: {$e->getMessage()}");
            throw $e;
        }
    }
}
