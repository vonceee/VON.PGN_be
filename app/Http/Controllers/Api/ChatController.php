<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function conversations(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::forUser($user->id)
            ->with(['latestMessage.sender', 'participants'])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->paginate(20);

        return ConversationResource::collection($conversations)
            ->additional([
                'meta' => [
                    'current_page' => $conversations->currentPage(),
                    'last_page' => $conversations->lastPage(),
                    'per_page' => $conversations->perPage(),
                    'total' => $conversations->total(),
                ],
            ]);
    }

    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $currentUser = $request->user();
        $otherUserId = (int) $request->input('user_id');

        if ($currentUser->id === $otherUserId) {
            return response()->json(['message' => 'Cannot start a conversation with yourself.'], 422);
        }

        $conversation = Conversation::getOrCreateBetween($currentUser->id, $otherUserId);
        $conversation->load(['latestMessage.sender', 'participants']);

        return new ConversationResource($conversation);
    }

    public function messages(Request $request, int $conversationId)
    {
        $user = $request->user();

        $conversation = Conversation::forUser($user->id)->find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->orderByDesc('created_at')
            ->paginate(30);

        $messageData = MessageResource::collection($messages);

        return $messageData->additional([
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function sendMessage(Request $request, int $conversationId)
    {
        $throttleKey = 'send-message:' . $request->user()->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 30)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => 'Rate limit exceeded. Please wait ' . $seconds . ' seconds.',
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($throttleKey, 60);

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|min:1|max:5000',
            'temp_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        $conversation = Conversation::forUser($user->id)->find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $sanitizedBody = strip_tags($request->input('body'));
        $sanitizedBody = trim($sanitizedBody);

        if (empty($sanitizedBody)) {
            return response()->json(['message' => 'Message body cannot be empty after sanitization.'], 422);
        }

        $message = DB::transaction(function () use ($conversation, $user, $sanitizedBody) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'body' => $sanitizedBody,
                'status' => 'sent',
            ]);

            $otherParticipants = $conversation->participants()
                ->where('users.id', '!=', $user->id)
                ->get();

            foreach ($otherParticipants as $participant) {
                $conversation->participants()
                    ->updateExistingPivot($participant->id, [
                        'unread_count' => DB::raw('unread_count + 1'),
                    ]);
            }

            $conversation->touch();

            return $message;
        });

        $message->load('sender');


        return response()->json([
            'data' => new MessageResource($message),
            'temp_id' => $request->input('temp_id'),
        ], 201);
    }

    public function markAsRead(Request $request, int $conversationId)
    {
        $user = $request->user();

        $conversation = Conversation::forUser($user->id)->find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $lastReadMessageId = $request->input('last_read_message_id');

        DB::transaction(function () use ($conversation, $user, $lastReadMessageId) {
            $conversation->participants()
                ->updateExistingPivot($user->id, [
                    'unread_count' => 0,
                    'last_read_at' => now(),
                ]);

            if ($lastReadMessageId) {
                Message::where('conversation_id', $conversation->id)
                    ->where('sender_id', '!=', $user->id)
                    ->where('id', '<=', $lastReadMessageId)
                    ->where('status', '!=', 'read')
                    ->update(['status' => 'read']);

            }
        });

        return response()->json(['message' => 'Messages marked as read.']);
    }

    public function typing(Request $request, int $conversationId)
    {
        $user = $request->user();

        $conversation = Conversation::forUser($user->id)->find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $isTyping = $request->boolean('is_typing', true);


        return response()->json(['ok' => true]);
    }

    public function updateStatus(Request $request)
    {
        $user = $request->user();
        $isOnline = $request->boolean('is_online', true);

        $user->update([
            'is_online' => $isOnline,
            'last_seen_at' => $isOnline ? null : now(),
        ]);


        return response()->json(['ok' => true]);
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $totalUnread = DB::table('conversation_user')
            ->where('user_id', $user->id)
            ->sum('unread_count');

        return response()->json(['unread_count' => $totalUnread ?? 0]);
    }
}
