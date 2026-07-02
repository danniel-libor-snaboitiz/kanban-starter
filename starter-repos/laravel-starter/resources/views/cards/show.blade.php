@extends('layouts.app')

@section('title', $card->title)

@section('content')
    <a href="{{ route('boards.show', $card->column->board_id) }}" class="text-sm text-gray-500 hover:text-black">&larr; back to {{ $card->column->board->name }}</a>

    <div class="mt-4 flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Card #{{ $card->id }} — {{ $card->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">Column: {{ $card->column->name }}</p>
        </div>
        <form method="POST" action="{{ route('cards.destroy', $card) }}"
              onsubmit="return confirm('Delete this card?')">
            @csrf
            @method('DELETE')
            <button class="rounded border border-red-300 px-2 py-1 text-sm text-red-600 hover:bg-red-50">Delete</button>
        </form>
    </div>

    @if ($card->description)
        <div class="mt-4 rounded border bg-white p-4">
            <h2 class="mb-1 text-sm font-medium text-gray-500">Description</h2>
            <p class="whitespace-pre-line">{{ $card->description }}</p>
        </div>
    @endif

    <div class="mt-6">
        <h2 class="mb-3 text-lg font-semibold">Comments ({{ $card->comments->count() }})</h2>

        <div class="space-y-3">
            @forelse ($card->comments as $comment)
                <div class="rounded border bg-white p-3">
                    <p class="text-xs text-gray-400">
                        {{ '@' . ($comment->user->username ?? 'unknown') }} · {{ $comment->created_at->diffForHumans() }}
                    </p>
                    <p class="mt-1">{!! $comment->bodyHtml() !!}</p>
                </div>
            @empty
                <p class="text-gray-500">No comments yet.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('comments.store', $card) }}" class="mt-4">
            @csrf
            <label for="body" class="block text-sm font-medium">Add a comment</label>
            <textarea id="body" name="body" rows="3" required
                      placeholder="Type @username to mention someone..."
                      class="mt-1 w-full rounded border-gray-300 shadow-sm">{{ old('body') }}</textarea>
            @error('body')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <button class="mt-2 rounded bg-black px-4 py-2 text-sm text-white hover:bg-gray-800">Post comment</button>
        </form>
    </div>
@endsection
