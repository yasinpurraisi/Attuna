<x-layout title="My Friends — Attuna">
    <main class="max-w-5xl mx-auto px-6 py-10 space-y-6">

        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-foreground">My Friends</h1>
                <p class="text-sm text-muted-foreground mt-1">
                    {{ $friends->count() }} connected
                    {{ $friends->count() === 1 ? 'person' : 'people' }}
                </p>
            </div>

            <a href="{{ route('users.index') }}" class="btn">
                Find People
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-border bg-muted px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse ($friends as $friend)
                <article class="flex items-center gap-4 rounded-2xl border border-border p-4">
                    <a href="{{ route('users.show', $friend) }}">
                        <img
                            src="{{ asset('images/avatars/' . $friend->avatar) }}"
                            alt=""
                            class="w-14 h-14 rounded-full object-cover"
                        >
                    </a>

                    <div class="min-w-0 flex-1">
                        <a href="{{ route('users.show', $friend) }}"
                           class="font-semibold text-foreground hover:underline">
                            {{ $friend->name }}
                        </a>
                        <p class="text-sm text-muted-foreground">
                            {{ '@' . $friend->username }}
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('friendships.destroy', $friend->friendship_id) }}"
                        onsubmit="return confirm('Remove this friend?')"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-sm text-red-600 hover:text-red-700">
                            Remove
                        </button>
                    </form>
                </article>
            @empty
                <div class="sm:col-span-2 rounded-2xl border border-dashed border-border p-8 text-center">
                    <p class="font-medium text-foreground">No friends yet</p>
                    <p class="text-sm text-muted-foreground mt-2">
                        Search for people and send a friend request.
                    </p>
                    <a href="{{ route('users.index') }}" class="btn mt-4">
                        Find Friends
                    </a>
                </div>
            @endforelse
        </div>
    </main>
</x-layout>