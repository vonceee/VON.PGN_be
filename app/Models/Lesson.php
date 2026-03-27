<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = ['chapter_id', 'title', 'slug', 'content_blocks', 'order'];

    protected $casts = [
        'content_blocks' => 'array',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
