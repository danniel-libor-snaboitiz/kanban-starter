@extends('layouts.app')

@section('title', $board->name)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="POST" action="{{ route('boards.update', $board) }}" class="flex items-center gap-2">
            @csrf
            @method('PATCH')
            <input name="name" value="{{ $board->name }}" class="rounded border-gray-300 text-xl font-semibold shadow-sm">
            <button class="rounded border px-2 py-1 text-sm hover:bg-gray-100">Rename</button>
        </form>
        <form method="POST" action="{{ route('boards.destroy', $board) }}"
              onsubmit="return confirm('Delete this board?')">
            @csrf
            @method('DELETE')
            <button class="rounded border border-red-300 px-2 py-1 text-sm text-red-600 hover:bg-red-50">Delete board</button>
        </form>
    </div>

    <div class="flex items-start gap-4 overflow-x-auto pb-4">
        @foreach ($board->columns as $column)
            <div class="w-72 flex-shrink-0 rounded bg-gray-100 p-3"
                 data-column-id="{{ $column->id }}"
                 ondragover="event.preventDefault()"
                 ondrop="dropCard(event, {{ $column->id }})">
                <div class="mb-2 flex items-center justify-between">
                    <h2 class="font-semibold">{{ $column->name }} <span class="text-gray-400">{{ $column->cards->count() }}</span></h2>
                    <form method="POST" action="{{ route('columns.destroy', $column) }}"
                          onsubmit="return confirm('Delete this column and its cards?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs text-gray-400 hover:text-red-600">✕</button>
                    </form>
                </div>

                <div class="space-y-2">
                    @foreach ($column->cards as $card)
                        <div class="rounded bg-white p-2 shadow-sm" draggable="true"
                             ondragstart="dragCard(event, {{ $card->id }})">
                            <a href="{{ route('cards.show', $card) }}" class="block font-medium hover:underline">{{ $card->title }}</a>
                            <div class="mt-1 flex items-center justify-between text-xs text-gray-400">
                                <span>#{{ $card->id }}</span>
                                {{-- No-JS move fallback --}}
                                <form method="POST" action="{{ route('cards.update', $card) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="column_id" onchange="this.form.submit()"
                                            class="rounded border-gray-200 text-xs">
                                        @foreach ($board->columns as $target)
                                            <option value="{{ $target->id }}" @selected($target->id === $column->id)>{{ $target->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('cards.store', $column) }}" class="mt-2">
                    @csrf
                    <input name="title" placeholder="+ Add card" required
                           class="w-full rounded border-gray-300 text-sm shadow-sm">
                </form>
            </div>
        @endforeach

        <form method="POST" action="{{ route('columns.store', $board) }}" class="w-72 flex-shrink-0">
            @csrf
            <input name="name" placeholder="+ Add column" required
                   class="w-full rounded border-gray-300 text-sm shadow-sm">
        </form>
    </div>

    <script>
        function dragCard(event, cardId) {
            event.dataTransfer.setData('text/card-id', cardId);
        }

        function dropCard(event, columnId) {
            event.preventDefault();
            const cardId = event.dataTransfer.getData('text/card-id');
            if (!cardId) return;

            fetch(`/cards/${cardId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ column_id: columnId }),
            }).then(() => window.location.reload());
        }
    </script>
@endsection
