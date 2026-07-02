@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">My Tasks</h1>
        <a href="{{ route('tasks.create') }}"
           class="rounded bg-black px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
            New Task
        </a>
    </div>

    @php
        $columns = ['todo' => 'Todo', 'doing' => 'Doing', 'done' => 'Done'];
        $badges = [
            'todo' => 'bg-gray-200 text-gray-800',
            'doing' => 'bg-yellow-200 text-yellow-800',
            'done' => 'bg-green-200 text-green-800',
        ];
    @endphp

    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($columns as $key => $label)
            <div class="rounded-lg border bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600">{{ $label }}</h2>
                    <span class="text-xs text-gray-500">
                        {{ $tasks->where('status', $key)->count() }}
                    </span>
                </div>

                <ul class="space-y-2">
                    @forelse ($tasks->where('status', $key) as $task)
                        <li class="rounded border border-gray-200 bg-gray-50 p-3">
                            <div class="flex items-start justify-between">
                                <a href="{{ route('tasks.show', $task) }}"
                                   class="font-medium text-gray-900 hover:underline">
                                    {{ $task->title }}
                                </a>
                                <span class="ml-2 rounded px-2 py-0.5 text-xs {{ $badges[$key] }}">
                                    {{ $label }}
                                </span>
                            </div>
                            <div class="mt-2 flex gap-3 text-xs">
                                <a href="{{ route('tasks.edit', $task) }}" class="text-gray-600 hover:text-black">Edit</a>
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400">No tasks.</li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>
@endsection
