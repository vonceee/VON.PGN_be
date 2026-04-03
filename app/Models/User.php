<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyEmail;
use App\Models\UserPreference;
use App\Models\UserProgress;
use App\Models\Badge;
use App\Models\Follow;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'email_verified_at',
        'bio',
        'is_admin',
        'verified_organizer',
        'last_seen_at',
        'is_online',
        // Live chess ratings
        'bullet_rating',
        'bullet_rd',
        'bullet_games',
        'blitz_rating',
        'blitz_rd',
        'blitz_games',
        'rapid_rating',
        'rapid_rd',
        'rapid_games',
        'last_game_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'verified_organizer' => 'boolean',
            'is_online' => 'boolean',
            'last_seen_at' => 'datetime',
            'last_game_at' => 'datetime',
            'bullet_rating' => 'integer',
            'bullet_rd' => 'integer',
            'bullet_games' => 'integer',
            'blitz_rating' => 'integer',
            'blitz_rd' => 'integer',
            'blitz_games' => 'integer',
            'rapid_rating' => 'integer',
            'rapid_rd' => 'integer',
            'rapid_games' => 'integer',
        ];
    }

    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function progress()
    {
        return $this->hasOne(UserProgress::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_user')
            ->withPivot('earned_at');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withPivot(['unread_count', 'last_read_at'])
            ->withTimestamps();
    }

    public function tournaments()
    {
        return $this->hasMany(Tournament::class, 'created_by');
    }

    public function bookmarkedTournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_bookmarks')->withTimestamps();
    }

    public function getLiveChessRatings(): array
    {
        return [
            'bullet' => [
                'rating' => $this->bullet_rating ?? 1500,
                'rd' => $this->bullet_rd ?? 350,
                'games' => $this->bullet_games ?? 0,
                'prov' => ($this->bullet_games ?? 0) < 10,
            ],
            'blitz' => [
                'rating' => $this->blitz_rating ?? 1500,
                'rd' => $this->blitz_rd ?? 350,
                'games' => $this->blitz_games ?? 0,
                'prov' => ($this->blitz_games ?? 0) < 10,
            ],
            'rapid' => [
                'rating' => $this->rapid_rating ?? 1500,
                'rd' => $this->rapid_rd ?? 350,
                'games' => $this->rapid_games ?? 0,
                'prov' => ($this->rapid_games ?? 0) < 10,
            ],
        ];
    }
}
