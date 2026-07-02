@extends('layouts.app')

@section('title', $task->title)

@section('content')
    <div class="mx-auto max-w-2xl rounded-lg border bg-white p-6 shadow-sm">
        <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:text-black">&larr; Back to tasks</a>

        <h1 class="mt-3 text-2xl font-semibold">{{ $task->title }}</h1>

        <div class="mt-2">
            <span class="rounded bg-gray-200 px-2 py-0.5 text-xs uppercase tracking-wide text-gray-800">
                {{ $task->status }}
            </span>
        </div>

        <p class="mt-4 whitespace-pre-line text-gray-700">
            {{ $task->description ?: 'No description.' }}
        </p>

        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('tasks.edit', $task) }}"
               class="rounded bg-black px-3 py-2 text-sm text-white hover:bg-gray-800">
                Edit
            </a>
            <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>
@endsection
