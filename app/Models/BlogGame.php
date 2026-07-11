<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'title',
        'pgn',
        'order',
    ];

    /**
     * The blog post this game belongs to.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }
}
