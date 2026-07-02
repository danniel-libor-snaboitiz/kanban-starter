<?php

namespace App\Services;

use App\Models\Comment;
use App\Support\MentionParser;

class NotificationService
{
    public function __construct(private MentionParser $parser) {}

    /**
     * Create a mention notification for every existing user named in a comment.
     *
     * @return int the number of notifications created
     */
    public function notifyMentioned(Comment $comment): int
    {
        $mentioned = $this->parser->resolve($comment->body);

        foreach ($mentioned as $user) {
            $user->notifications()->create([
                'actor_id' => $comment->user_id,
                'card_id' => $comment->card_id,
                'type' => 'mention',
            ]);
        }

        return $mentioned->count();
    }
}
