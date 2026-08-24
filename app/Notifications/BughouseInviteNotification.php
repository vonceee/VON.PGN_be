<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class BughouseInviteNotification extends Notification
{
    use Queueable;

    protected $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $sender)
    {
        $this->sender = $sender;
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
        $senderRating = 1600; 
        $inviteId = uniqid('bg_', true);

        return [
            'message' => "{$this->sender->name} has invited you to a Bughouse chess match.",
            'action_url' => "/bughouse?inviteId={$inviteId}&sender={$this->sender->name}&senderId={$this->sender->id}&rating={$senderRating}",
            'type' => 'bughouse_invite',
            'sender_username' => $this->sender->name,
            'sender_rating' => $senderRating,
            'invite_id' => $inviteId
        ];
    }
}
