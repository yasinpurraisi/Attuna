<x-layout title="Find People — Attuna">
    <main class="max-w-5xl mx-auto px-6 py-10 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-foreground">Find People</h1>
            <p class="text-muted-foreground mt-1">
                Discover people and connect with them on Attuna.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-border bg-muted px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('users.index') }}"
              class="flex gap-2">
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Search by name or username..."
                class="input min-w-0 flex-1"
                maxlength="50"
            >
            <button type="submit" class="btn">Search</button>

            @if ($search !== '')
                <a href="{{ route('users.index') }}"
                   class="btn btn-outlined">
                    Clear
                </a>
            @endif
        </form>

        <p class="text-sm text-muted-foreground">
            {{ $users->total() }} {{ $users->total() === 1 ? 'person' : 'people' }} found
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse ($users as $person)
                @php
                    $friendship = $friendshipsByUserId->get($person->id);
                    $isIncoming = $friendship
                        && $friendship->status === 'pending'
                        && (int) $friendship->receiver_id === (int) auth()->id();
                    $isOutgoing = $friendship
                        && $friendship->status === 'pending'
                        && (int) $friendship->sender_id === (int) auth()->id();
                @endphp

                <article class="rounded-2xl border border-border p-4 flex items-center gap-4">
                    <img
                        src="{{ asset('images/avatars/' . $person->avatar) }}"
                        alt=""
                        class="w-14 h-14 rounded-full object-cover"
                    >

                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-foreground truncate">
                            {{ $person->name }}
                        </p>
                        <p class="text-sm text-muted-foreground truncate">
                            {{ '@' . $person->username }}
                        </p>
                    </div>

                    <div class="shrink-0">
                        @if (! $friendship)
                            <form method="POST"
                                  action="{{ route('friendships.store', $person) }}">
                                @csrf
                                <button type="submit" class="btn btn-outlined">
                                    Add Friend
                                </button>
                            </form>

                        @elseif ($friendship->status === 'accepted')
                            <div class="flex flex-col items-end gap-2">
                                <span class="text-sm text-muted-foreground">Friends</span>

                                <form
                                    method="POST"
                                    action="{{ route('friendships.destroy', $friendship) }}"
                                    onsubmit="return confirm('Remove {{ addslashes($person->name) }} as a friend?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-sm text-red-600 hover:text-red-700">
                                        Remove
                                    </button>
                                </form>
                            </div>

                        @elseif ($isIncoming)
                            <div class="flex flex-col items-end gap-2">
                                <form method="POST"
                                      action="{{ route('friendships.accept', $friendship) }}">
                                    @csrf
                                    <button type="submit" class="btn">
                                        Accept
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('friendships.destroy', $friendship) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-sm text-muted-foreground hover:text-foreground">
                                        Decline
                                    </button>
                                </form>
                            </div>

                        @elseif ($isOutgoing)
                            <div class="text-right space-y-2">
                                <p class="text-sm text-muted-foreground">Request sent</p>
                                <form method="POST"
                                      action="{{ route('friendships.destroy', $friendship) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-sm text-muted-foreground hover:text-foreground">
                                        Cancel
                                    </button>
                                </form>
                            </div>

                        @else
                            <span class="text-sm text-muted-foreground">
                                Unavailable
                            </span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="sm:col-span-2 rounded-2xl border border-dashed border-border p-8 text-center">
                    <p class="font-medium text-foreground">No users found</p>
                    <p class="text-sm text-muted-foreground mt-2">
                        Try a different name or username.
                    </p>
                </div>
            @endforelse
        </div>

        <div>
            {{ $users->links() }}
        </div>
    </main>
</x-layout>