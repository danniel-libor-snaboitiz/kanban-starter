<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\MentionParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentionParserTest extends TestCase
{
    use RefreshDatabase;

    private MentionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new MentionParser;
    }

    public function test_it_extracts_a_simple_mention(): void
    {
        $this->assertSame(['alice'], $this->parser->extractHandles('hey @alice look'));
    }

    public function test_it_dedupes_and_lowercases_repeated_mentions(): void
    {
        $this->assertSame(['alice'], $this->parser->extractHandles('@alice @Alice @ALICE'));
    }

    public function test_it_honors_trailing_punctuation_boundaries(): void
    {
        $this->assertSame(['alice'], $this->parser->extractHandles('thanks @alice, appreciated'));
    }

    public function test_it_does_not_match_inside_an_email(): void
    {
        $this->assertSame([], $this->parser->extractHandles('mail me at x@alice.com'));
    }

    public function test_resolve_returns_only_existing_users(): void
    {
        User::factory()->create(['username' => 'alice']);

        $resolved = $this->parser->resolve('ping @alice and @ghost');

        $this->assertCount(1, $resolved);
        $this->assertSame('alice', $resolved->first()->username);
    }

    public function test_resolve_is_case_insensitive(): void
    {
        $alice = User::factory()->create(['username' => 'alice']);

        $resolved = $this->parser->resolve('hello @ALICE');

        $this->assertTrue($resolved->contains('id', $alice->id));
    }

    public function test_resolve_returns_distinct_users_for_repeated_mentions(): void
    {
        User::factory()->create(['username' => 'alice']);

        $resolved = $this->parser->resolve('@alice @alice @alice');

        $this->assertCount(1, $resolved);
    }

    public function test_resolve_handles_a_self_mention_like_any_other(): void
    {
        $me = User::factory()->create(['username' => 'me']);

        $resolved = $this->parser->resolve('note to @me');

        $this->assertTrue($resolved->contains('id', $me->id));
    }

    public function test_resolve_returns_empty_when_there_are_no_mentions(): void
    {
        User::factory()->create(['username' => 'alice']);

        $this->assertTrue($this->parser->resolve('no mentions here')->isEmpty());
    }
}
