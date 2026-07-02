@extends('layouts.app')

@section('title', 'New Task')

@section('content')
    <div class="mx-auto max-w-lg rounded-lg border bg-white p-6 shadow-sm">
        <h1 class="mb-4 text-xl font-semibold">New Task</h1>

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 px-3 py-2 text-sm text-red-800">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required
                    class="mt-1 w-full rounded border px-3 py-2">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="mt-1 w-full rounded border px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium">Status</label>
                <select id="status" name="status" class="mt-1 w-full rounded border px-3 py-2">
                    <option value="todo" {{ old('status') === 'todo' ? 'selected' : '' }}>Todo</option>
                    <option value="doing" {{ old('status') === 'doing' ? 'selected' : '' }}>Doing</option>
                    <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded bg-black px-3 py-2 text-white hover:bg-gray-800">
                    Create
                </button>
                <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:text-black">Cancel</a>
            </div>
        </form>
    </div>
@endsection
