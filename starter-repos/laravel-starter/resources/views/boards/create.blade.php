@extends('layouts.app')

@section('title', 'New board')

@section('content')
    <h1 class="mb-6 text-2xl font-semibold">New board</h1>

    <form method="POST" action="{{ route('boards.store') }}" class="max-w-md space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium">Board name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                   class="mt-1 w-full rounded border-gray-300 shadow-sm">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded bg-black px-4 py-2 text-sm text-white hover:bg-gray-800">Create board</button>
            <a href="{{ route('boards.index') }}" class="text-sm text-gray-600 hover:text-black">Cancel</a>
        </div>
    </form>
@endsection
