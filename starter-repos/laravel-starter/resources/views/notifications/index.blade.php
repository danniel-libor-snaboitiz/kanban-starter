@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <h1 class="mb-6 text-2xl font-semibold">Notifications</h1>

    @if ($notifications->isEmpty())
        <p class="text-gray-500">No notifications yet.</p>
    @else
        <ul class="space-y-2">
            @foreach ($notifications as $notification)
                <li class="rounded border bg-white p-3 {{ $notification->read_at ? 'opacity-60' : 'border-l-4 border-l-blue-600' }}">
                    <form method="POST" action="{{ route('notifications.update', $notification) }}" class="flex items-center justify-between gap-3">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-left">
                            @unless ($notification->read_at)
                                <span class="mr-1 text-blue-600">●</span>
                            @endunless
                            <span class="font-medium">{{ '@' . ($notification->actor->username ?? 'someone') }}</span>
                            mentioned you on
                            <span class="font-medium">“{{ $notification->card->title ?? 'a card' }}”</span>
                        </button>
                        <span class="whitespace-nowrap text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
