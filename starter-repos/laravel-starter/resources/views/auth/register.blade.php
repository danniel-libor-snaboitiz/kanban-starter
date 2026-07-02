@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="mx-auto max-w-md rounded-lg border bg-white p-6 shadow-sm">
        <h1 class="mb-4 text-xl font-semibold">Create account</h1>

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 px-3 py-2 text-sm text-red-800">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                    class="mt-1 w-full rounded border px-3 py-2">
            </div>

            <div>
                <label for="username" class="block text-sm font-medium">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required
                    class="mt-1 w-full rounded border px-3 py-2" placeholder="used for @mentions">
            </div>

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

            <div>
                <label for="password_confirmation" class="block text-sm font-medium">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-1 w-full rounded border px-3 py-2">
            </div>

            <button type="submit" class="w-full rounded bg-black px-3 py-2 text-white hover:bg-gray-800">
                Register
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-600">
            Already have an account? <a href="{{ route('login') }}" class="text-black underline">Log in</a>
        </p>
    </div>
@endsection
