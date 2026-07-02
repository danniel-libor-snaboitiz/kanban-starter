@extends('layouts.app')

@section('title', 'Boards')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">My Boards</h1>
        <a href="{{ route('boards.create') }}" class="rounded bg-black px-3 py-1.5 text-sm text-white hover:bg-gray-800">New board</a>
    </div>

    @if ($boards->isEmpty())
        <p class="text-gray-500">No boards yet. Create your first one.</p>
    @else
        <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($boards as $board)
                <li class="rounded border bg-white p-4 shadow-sm">
                    <a href="{{ route('boards.show', $board) }}" class="text-lg font-medium hover:underline">{{ $board->name }}</a>
                    <p class="mt-1 text-sm text-gray-500">{{ $board->columns()->count() }} columns</p>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
