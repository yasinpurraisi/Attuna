
<x-layout>
    @php
        $statusValue = $tune->status->value;

        $statusClasses = match ($statusValue) {
            'pending' => 'border-amber-300 bg-amber-50 text-amber-800 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
            'in_progress' => 'border-blue-300 bg-blue-50 text-blue-800 dark:border-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
            'completed' => 'border-green-300 bg-green-50 text-green-800 dark:border-green-700 dark:bg-green-950/40 dark:text-green-300',
            default => 'border-border bg-muted text-foreground',
        };
    @endphp

    <div class="mx-auto max-w-4xl px-4 py-8">
        {{-- Navigation and actions --}}
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <a
                href="{{ route('tunes.index') }}"
                class="text-sm font-medium text-muted-foreground transition hover:text-foreground"
            >
                ← Back to Tunes
            </a>

            @if ($isOwner)
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="btn btn-outlined"
                        x-data
                        @click="$dispatch('open-modal', 'edit_tune')"
                    >
                        Edit
                    </button>

                    <form
                        action="{{ route('tunes.destroy', $tune) }}"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this tune?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-outlined hover:text-red-500"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            @else
                <span class="rounded-full border border-border px-3 py-1.5 text-xs font-medium text-muted-foreground">
                    Shared with you
                </span>
            @endif
        </div>

        {{-- Success message --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-border bg-muted p-4 text-sm text-foreground">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-7">
            {{-- Featured image --}}
            @if ($tune->image_path)
                <div class="overflow-hidden rounded-2xl border border-border">
                    <img
                        src="{{ asset('storage/' . $tune->image_path) }}"
                        alt=""
                        class="max-h-[28rem] w-full object-cover"
                    >
                </div>
            @endif

            {{-- Title and metadata --}}
            <section>
                <h1 class="text-3xl font-bold tracking-tight text-foreground md:text-4xl">
                    {{ $tune->title }}
                </h1>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium {{ $statusClasses }}">
                        {{ $tune->status->label() }}
                    </span>

                    <span class="text-sm text-muted-foreground">
                        {{ $tune->created_at->diffForHumans() }}
                    </span>

                    <span class="text-sm text-muted-foreground">
                        By {{ '@' . $tune->user->username }}
                    </span>
                </div>
            </section>

            {{-- Description --}}
            @if ($tune->description)
                <x-card>
                    <div class="prose prose-invert max-w-none whitespace-pre-line text-foreground">
                        {{ $tune->description }}
                    </div>
                </x-card>
            @endif

            {{-- Sharing information --}}
            @if ($isOwner)
                <section class="rounded-2xl border border-border p-4 md:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-semibold text-foreground">
                            Shared with
                        </h2>

                        <span class="rounded-full bg-muted px-2.5 py-1 text-xs text-muted-foreground">
                            {{ $tune->sharedWith->count() }}
                        </span>
                    </div>

                    @if ($tune->sharedWith->isEmpty())
                        <p class="mt-3 text-sm text-muted-foreground">
                            This tune is private. Edit it to share it with your friends.
                        </p>

                        <button
                            type="button"
                            class="btn btn-outlined mt-4"
                            x-data
                            @click="$dispatch('open-modal', 'edit_tune')"
                        >
                            Share Tune
                        </button>
                    @else
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach ($tune->sharedWith as $friend)
                                <div class="flex items-center gap-2 rounded-full border border-border px-3 py-2">
                                    <img
                                        src="{{ asset('images/avatars/' . $friend->avatar) }}"
                                        alt=""
                                        class="size-8 rounded-full object-cover"
                                    >

                                    <div>
                                        <p class="text-sm font-medium text-foreground">
                                            {{ $friend->name }}
                                        </p>

                                        <p class="text-xs text-muted-foreground">
                                            {{ '@' . $friend->username }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            {{-- Tasks --}}
            <section>
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-bold text-foreground">
                        Tasks
                    </h2>

                    <span class="text-xs text-muted-foreground">
                        {{ $tune->tasks->where('completed', true)->count() }}
                        / {{ $tune->tasks->count() }} completed
                    </span>
                </div>

                <div class="mt-3 space-y-2">
                    @forelse ($tune->tasks as $task)
                        <x-card>
                            <form
                                method="POST"
                                action="{{ route('tasks.update', $task) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="flex items-center gap-3">
                                    <button
                                        type="submit"
                                        aria-label="{{ $task->completed ? 'Mark incomplete' : 'Mark complete' }}"
                                        class="flex size-5 shrink-0 items-center justify-center rounded-md transition {{ $task->completed ? 'bg-primary text-primary-foreground' : 'border border-border hover:border-primary' }}"
                                    >
                                        @if ($task->completed)
                                            ✓
                                        @endif
                                    </button>

                                    <span class="text-sm text-foreground {{ $task->completed ? 'text-muted-foreground line-through' : '' }}">
                                        {{ $task->description }}
                                    </span>
                                </div>
                            </form>
                        </x-card>
                    @empty
                        <div class="rounded-xl border border-dashed border-border p-6 text-center">
                            <p class="text-sm text-muted-foreground">
                                No tasks added to this tune yet.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Links --}}
            <section>
                <h2 class="text-xl font-bold text-foreground">
                    Links
                </h2>

                <div class="mt-3 space-y-2">
                    @forelse ($tune->links as $link)
                        <x-card :href="$link" class="flex items-center gap-3 text-sm font-medium text-primary transition hover:text-primary/80">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted">
                                ↗
                            </span>

                            <span class="break-all">{{ $link }}</span>
                        </x-card>
                    @empty
                        <div class="rounded-xl border border-dashed border-border p-6 text-center">
                            <p class="text-sm text-muted-foreground">
                                No links added to this tune yet.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Edit modal: owner only --}}
        @if ($isOwner)
            <x-tune.modal :tune="$tune" :friends="$friends" />
        @endif
    </div>
</x-layout>
