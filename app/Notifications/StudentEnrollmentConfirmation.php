<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AcademyEnrollment;

class StudentEnrollmentConfirmation extends Notification
{
    use Queueable;

    protected $enrollment;

    /**
     * Create a new notification instance.
     */
    public function __construct(AcademyEnrollment $enrollment)
    {
        $this->enrollment = $enrollment;
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
            ->subject('VonChess Academy Enrollment Received')
            ->greeting('Hello ' . $this->enrollment->full_name . '!')
            ->line('Thank you for enrolling in VonChess Academy.')
            ->line('We have received your enrollment request for the level: ' . $this->enrollment->chess_level . '.')
            ->line('Our team will review your application and contact you shortly via ' . $this->enrollment->contact_number . ' or email.')
            ->line('Stay tuned and keep practicing!')
            ->salutation('Best regards, The VonChess Team');
    }
}
