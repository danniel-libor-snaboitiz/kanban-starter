<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <nav class="border-b bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
            <a href="{{ url('/') }}" class="font-semibold">{{ config('app.name') }}</a>
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('boards.index') }}" class="text-gray-700 hover:text-black">Boards</a>
                    <a href="{{ route('notifications.index') }}" class="text-gray-700 hover:text-black">
                        Notifications
                        @if (($unreadCount ?? 0) > 0)
                            <span class="ml-1 rounded-full bg-red-600 px-2 py-0.5 text-xs font-semibold text-white">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('tasks.index') }}" class="text-gray-700 hover:text-black">Tasks</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-black">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-black">Login</a>
                    <a href="{{ route('register') }}" class="rounded bg-black px-3 py-1.5 text-white hover:bg-gray-800">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-4 py-8">
        @if (session('status'))
            <div class="mb-4 rounded bg-green-100 px-4 py-2 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
