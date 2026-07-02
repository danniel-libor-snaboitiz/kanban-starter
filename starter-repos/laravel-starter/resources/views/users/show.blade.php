@extends('layouts.app')

@section('title', '@' . $user->username)

@section('content')
    <div class="rounded border bg-white p-6">
        <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
        <p class="mt-1 text-gray-500">{{ '@' . $user->username }}</p>
        <p class="mt-4 text-sm text-gray-400">Placeholder profile page.</p>
    </div>
@endsection
