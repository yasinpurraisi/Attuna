<x-layout.model name="add_friend" title="Find Friends">
    <div class="space-y-5">

        <div>
            <h3 class="font-semibold text-foreground">People you may know</h3>
            <p class="text-sm text-muted-foreground mt-1">
                Send a friend request to connect with someone on Attuna.
            </p>
        </div>

        <form method="GET" action="{{ route('users.index') }}">
            <div class="flex gap-2">
                <input
                    type="text"
                    name="q"
                    placeholder="Search by name or username..."
                    class="input min-w-0 flex-1"
                    maxlength="50"
                >
                <button type="submit" class="btn">
                    Search
                </button>
            </div>
        </form>

        <div class="space-y-3">
            @forelse ($suggestedUsers as $suggestedUser)
                <div class="flex items-center gap-3 rounded-xl border border-border p-3">
                    <img
                        src="{{ asset('images/avatars/' . $suggestedUser->avatar) }}"
                        alt=""
                        class="w-12 h-12 rounded-full object-cover"
                    >

                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-foreground truncate">
                            {{ $suggestedUser->name }}
                        </p>
                        <p class="text-sm text-muted-foreground truncate">
                            {{ '@' . $suggestedUser->username }}
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('friendships.store', $suggestedUser) }}"
                    >
                        @csrf
                        <button type="submit" class="btn btn-outlined">
                            Add
                        </button>
                    </form>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-border p-6 text-center">
                    <p class="text-sm text-muted-foreground">
                        No new people to suggest right now.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="flex justify-end border-t border-border pt-4">
            <button
                type="button"
                class="btn btn-outlined"
                @click="$dispatch('close-modal')"
            >
                Close
            </button>
        </div>
    </div>
</x-layout.model>