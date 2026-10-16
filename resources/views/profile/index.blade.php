<x-layout title="My Profile — Attuna">
    <main class="max-w-5xl mx-auto px-6 py-10 space-y-8">

        {{-- Flash message --}}
        @if (session('success'))
            <div class="rounded-xl border border-border bg-muted px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Profile header --}}
        <section class="rounded-2xl border border-border bg-background p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                <img
                    src="{{ asset('images/avatars/' . $user->avatar) }}"
                    alt="{{ $user->name }}'s avatar"
                    class="w-24 h-24 rounded-full object-cover border border-border"
                >

                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-foreground">
                        {{ $user->name }}
                    </h1>

                    <p class="mt-1 text-muted-foreground">
                        {{ '@' . $user->username }}
                    </p>

                    <p class="mt-3 text-sm text-muted-foreground">
                        {{ $friends->count() }}
                        {{ $friends->count() === 1 ? 'friend' : 'friends' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('friendships.requests') }}"
                       class="btn btn-outlined">
                        Friend Requests
                    </a>

                    <button
                        type="button"
                        class="btn"
                        x-data
                        @click="$dispatch('open-modal', 'edit_profile')"
                    >
                        Edit Profile
                    </button>
                </div>
            </div>
        </section>

        {{-- Friends --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-foreground">
                        Your Friends
                    </h2>
                    <p class="text-sm text-muted-foreground mt-1">
                        People you're connected with on Attuna.
                    </p>
                </div>

                <button
                    type="button"
                    class="btn btn-outlined shrink-0"
                    x-data
                    @click="$dispatch('open-modal', 'add_friend')"
                >
                    <span class="mr-1">+</span> Add Friend
                </button>
            </div>

            @if ($friends->isEmpty())
                <div class="rounded-2xl border border-dashed border-border p-8 text-center">
                    <h3 class="font-medium text-foreground">No friends yet</h3>
                    <p class="text-sm text-muted-foreground mt-2">
                        Find people on Attuna and send them a friend request.
                    </p>

                    <button
                        type="button"
                        class="btn mt-4"
                        x-data
                        @click="$dispatch('open-modal', 'add_friend')"
                    >
                        Find Friends
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($friends as $friend)
                        <article
                            class="flex items-center gap-4 rounded-2xl border border-border p-4"
                            key="{{ $friend->id }}"
                        >
                            <a href="{{ route('users.show', $friend) }}"
                               class="shrink-0">
                                <img
                                    src="{{ asset('images/avatars/' . $friend->avatar) }}"
                                    alt="{{ $friend->name }}'s avatar"
                                    class="w-14 h-14 rounded-full object-cover"
                                >
                            </a>

                            <div class="min-w-0 flex-1">
                                <a
                                    href="{{ route('users.show', $friend) }}"
                                    class="font-semibold text-foreground hover:underline truncate block"
                                >
                                    {{ $friend->name }}
                                </a>

                                <p class="text-sm text-muted-foreground truncate">
                                    {{ '@' . $friend->username }}
                                </p>
                            </div>

                            <form
                                method="POST"
                                action="{{ route('friendships.destroy', $friend->friendship_id ?? 0) }}"
                                onsubmit="return confirm('Remove this friend?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="text-sm text-red-600 hover:text-red-700"
                                >
                                    Remove
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>

                <a href="{{ route('friends.index') }}"
                   class="inline-block text-sm text-muted-foreground hover:text-foreground">
                    View all friends →
                </a>
            @endif
        </section>
    </main>

    {{-- Existing edit profile modal --}}
    @include('profile.edit-modal')

    {{-- Add friend modal --}}
    @include('profile.add-friend-modal')
</x-layout>