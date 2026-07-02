<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentMentionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a card owned by the given user.
     */
    private function cardOwnedBy(User $owner): Card
    {
        $board = Board::factory()->for($owner)->create();
        $column = Column::factory()->for($board)->create();

        return Card::factory()->for($column)->create();
    }

    public function test_posting_a_comment_with_a_mention_creates_a_notification(): void
    {
        $author = User::factory()->create(['username' => 'me']);
        $alice = User::factory()->create(['username' => 'alice']);
        $card = $this->cardOwnedBy($author);

        $this->actingAs($author)
            ->post("/cards/{$card->id}/comments", ['body' => 'over to you @alice'])
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $alice->id,
            'actor_id' => $author->id,
            'card_id' => $card->id,
            'type' => 'mention',
            'read_at' => null,
        ]);
        $this->assertSame(1, Notification::count());
    }

    public function test_the_comment_itself_is_persisted(): void
    {
        $author = User::factory()->create(['username' => 'me']);
        $card = $this->cardOwnedBy($author);

        $this->actingAs($author)
            ->post("/cards/{$card->id}/comments", ['body' => 'just a note']);

        $this->assertDatabaseHas('comments', [
            'card_id' => $card->id,
            'user_id' => $author->id,
            'body' => 'just a note',
        ]);
    }

    public function test_a_comment_with_an_unknown_handle_creates_no_notifications(): void
    {
        $author = User::factory()->create(['username' => 'me']);
        $card = $this->cardOwnedBy($author);

        $this->actingAs($author)
            ->post("/cards/{$card->id}/comments", ['body' => 'hello @ghost'])
            ->assertRedirect();

        $this->assertSame(0, Notification::count());
    }

    public function test_a_repeated_mention_creates_a_single_notification(): void
    {
        $author = User::factory()->create(['username' => 'me']);
        User::factory()->create(['username' => 'alice']);
        $card = $this->cardOwnedBy($author);

        $this->actingAs($author)
            ->post("/cards/{$card->id}/comments", ['body' => '@alice @alice @alice']);

        $this->assertSame(1, Notification::count());
    }

    public function test_mentioning_multiple_users_creates_a_notification_each(): void
    {
        $author = User::factory()->create(['username' => 'me']);
        User::factory()->create(['username' => 'alice']);
        User::factory()->create(['username' => 'bob']);
        $card = $this->cardOwnedBy($author);

        $this->actingAs($author)
            ->post("/cards/{$card->id}/comments", ['body' => 'cc @alice and @bob']);

        $this->assertSame(2, Notification::count());
    }
}
