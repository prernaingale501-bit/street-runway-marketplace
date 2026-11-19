<?php

namespace App\Notifications;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationReviewed extends Notification
{
    use Queueable;

    public function __construct(public VendorApplication $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)->subject('Your Street Runway seller application was reviewed');
        if ($this->application->status === 'approved') {
            return $message->greeting('Welcome, '.$notifiable->name)->line('Your seller application for '.$this->application->store_name.' has been approved. You can now access seller tools.');
        }

        return $message->greeting('Hello, '.$notifiable->name)->line('Your seller application for '.$this->application->store_name.' was not approved at this time.')->line($this->application->rejection_reason ?: 'Please contact marketplace support if you need more information.');
    }
}
