<x-layout title="{{ $user->name }} — Attuna">
    <main class="max-w-3xl mx-auto px-6 py-10">

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-border bg-muted px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-2xl border border-border p-8 text-center">
            <img
                src="{{ asset('images/avatars/' . $user->avatar) }}"
                alt=""
                class="w-28 h-28 rounded-full object-cover mx-auto"
            >

            <h1 class="text-2xl font-bold text-foreground mt-4">
                {{ $user->name }}
            </h1>

            <p class="text-muted-foreground mt-1">
                {{ '@' . $user->username }}
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                @if (! $friendship)
                    <form method="POST"
                          action="{{ route('friendships.store', $user) }}">
                        @csrf
                        <button class="btn" type="submit">Add Friend</button>
                    </form>

                @elseif ($friendship->status === 'accepted')
                    <form method="POST"
                          action="{{ route('friendships.destroy', $friendship) }}"
                          onsubmit="return confirm('Remove this friend?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outlined" type="submit">
                            Remove Friend
                        </button>
                    </form>

                @elseif ((int) $friendship->receiver_id === (int) auth()->id())
                    <form method="POST"
                          action="{{ route('friendships.accept', $friendship) }}">
                        @csrf
                        <button class="btn" type="submit">
                            Accept Request
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('friendships.destroy', $friendship) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outlined" type="submit">
                            Decline
                        </button>
                    </form>

                @else
                    <span class="rounded-xl border border-border px-4 py-2 text-sm text-muted-foreground">
                        Friend request sent
                    </span>

                    <form method="POST"
                          action="{{ route('friendships.destroy', $friendship) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outlined" type="submit">
                            Cancel Request
                        </button>
                    </form>
                @endif
            </div>
        </section>

        <div class="mt-6">
            <a href="{{ route('users.index') }}"
               class="text-sm text-muted-foreground hover:text-foreground">
                ← Back to people
            </a>
        </div>
    </main>
</x-layout>