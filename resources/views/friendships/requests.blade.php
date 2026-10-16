<x-layout title="Friend Requests — Attuna">
    <main class="max-w-4xl mx-auto px-6 py-10 space-y-8">

        <div>
            <h1 class="text-2xl font-bold text-foreground">Friend Requests</h1>
            <p class="text-sm text-muted-foreground mt-1">
                Manage incoming and outgoing requests.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-border bg-muted px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Incoming --}}
        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">
                Incoming Requests
                <span class="text-sm text-muted-foreground">
                    ({{ $incomingRequests->count() }})
                </span>
            </h2>

            @forelse ($incomingRequests as $friendship)
                <article class="flex items-center gap-4 rounded-xl border border-border p-4">
                    <img
                        src="{{ asset('images/avatars/' . $friendship->sender->avatar) }}"
                        alt=""
                        class="w-12 h-12 rounded-full object-cover"
                    >

                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-foreground">
                            {{ $friendship->sender->name }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {{ '@' . $friendship->sender->username }}
                        </p>
                    </div>

                    <form method="POST"
                          action="{{ route('friendships.accept', $friendship) }}">
                        @csrf
                        <button class="btn" type="submit">Accept</button>
                    </form>

                    <form method="POST"
                          action="{{ route('friendships.destroy', $friendship) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outlined" type="submit">
                            Decline
                        </button>
                    </form>
                </article>
            @empty
                <p class="text-sm text-muted-foreground">
                    You don't have any incoming requests.
                </p>
            @endforelse
        </section>

        {{-- Outgoing --}}
        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">
                Sent Requests
                <span class="text-sm text-muted-foreground">
                    ({{ $outgoingRequests->count() }})
                </span>
            </h2>

            @forelse ($outgoingRequests as $friendship)
                <article class="flex items-center gap-4 rounded-xl border border-border p-4">
                    <img
                        src="{{ asset('images/avatars/' . $friendship->receiver->avatar) }}"
                        alt=""
                        class="w-12 h-12 rounded-full object-cover"
                    >

                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-foreground">
                            {{ $friendship->receiver->name }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {{ '@' . $friendship->receiver->username }}
                        </p>
                    </div>

                    <form method="POST"
                          action="{{ route('friendships.destroy', $friendship) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-sm text-muted-foreground hover:text-foreground">
                            Cancel Request
                        </button>
                    </form>
                </article>
            @empty
                <p class="text-sm text-muted-foreground">
                    You haven't sent any pending requests.
                </p>
            @endforelse
        </section>
    </main>
</x-layout>