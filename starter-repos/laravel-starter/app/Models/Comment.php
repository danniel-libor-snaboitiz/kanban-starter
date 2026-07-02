<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'card_id',
        'user_id',
        'body',
    ];

    /**
     * The card this comment is on.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * The user that wrote this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The comment body as safe HTML, with @mentions turned into profile links.
     * The body is HTML-escaped first, then handles (safe [A-Za-z0-9_] chars) are
     * wrapped in anchors, so the result is safe to render unescaped.
     */
    public function bodyHtml(): string
    {
        $escaped = e($this->body);

        return preg_replace_callback(
            '/(?<!\w)@([A-Za-z0-9_]+)/u',
            fn ($m) => '<a href="'.route('users.show', $m[1]).'" class="font-medium text-blue-600 hover:underline">@'.$m[1].'</a>',
            $escaped,
        );
    }
}
