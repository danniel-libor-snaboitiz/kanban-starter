@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="mx-auto max-w-md rounded-lg border bg-white p-6 shadow-sm">
        <h1 class="mb-4 text-xl font-semibold">Login</h1>

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 px-3 py-2 text-sm text-red-800">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="mt-1 w-full rounded border px-3 py-2">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 w-full rounded border px-3 py-2">
            </div>

            <button type="submit" class="w-full rounded bg-black px-3 py-2 text-white hover:bg-gray-800">
                Log in
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-600">
            No account? <a href="{{ route('register') }}" class="text-black underline">Register</a>
        </p>
    </div>
@endsection
