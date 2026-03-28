<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUserId = $request->user()?->id;

        $otherParticipant = $this->participants
            ->first(fn ($p) => $p->id !== $currentUserId);

        $latestMessage = $this->whenLoaded('latestMessage');

        $unreadCount = 0;
        if ($currentUserId) {
            $pivot = $this->participants
                ->first(fn ($p) => $p->id === $currentUserId)
                ?->pivot;
            $unreadCount = $pivot?->unread_count ?? 0;
        }

        return [
            'id' => $this->id,
            'other_user' => $otherParticipant ? [
                'id' => $otherParticipant->id,
                'name' => $otherParticipant->name,
                'is_online' => $otherParticipant->is_online,
                'last_seen_at' => $otherParticipant->last_seen_at?->toISOString(),
            ] : null,
            'latest_message' => $latestMessage ? new MessageResource($latestMessage) : null,
            'unread_count' => $unreadCount,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
