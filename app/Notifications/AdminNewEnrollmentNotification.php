<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AcademyEnrollment;

class AdminNewEnrollmentNotification extends Notification
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
            ->subject('New Academy Enrollment: ' . $this->enrollment->full_name)
            ->greeting('Hello Admin!')
            ->line('A new enrollment has been submitted for VonChess Academy.')
            ->line('**Name:** ' . $this->enrollment->full_name)
            ->line('**Email:** ' . $this->enrollment->email)
            ->line('**Contact:** ' . $this->enrollment->contact_number)
            ->line('**Level:** ' . $this->enrollment->chess_level)
            ->line('**Experience:** ' . ($this->enrollment->experience ?? 'None provided'))
            ->action('View All Enrollments', env('FRONTEND_URL', 'http://localhost:4200') . '/admin/academy-enrollments')
            ->line('Please review and contact the student to finalize their enrollment.');
    }
}
