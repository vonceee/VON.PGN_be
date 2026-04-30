<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Study;

class CollaboratorAddedNotification extends Notification
{
    use Queueable;

    protected $study;

    /**
     * Create a new notification instance.
     */
    public function __construct(Study $study)
    {
        $this->study = $study;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'study_id' => $this->study->id,
            'study_name' => $this->study->name,
            'owner_name' => $this->study->owner->name,
            'message' => "You have been added as a collaborator on the study \"{$this->study->name}\" by {$this->study->owner->name}.",
            'action_url' => "/study/{$this->study->id}",
            'type' => 'collaborator_added'
        ];
    }
}
