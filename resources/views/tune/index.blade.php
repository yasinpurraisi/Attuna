
<x-layout>
    <div class="text-muted-foreground">
        {{-- Header --}}
        <header class="py-8 md:py-10">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-primary">Tunes</h1>
                    <p class="mt-2 text-sm">
                        Capture your thoughts. Make a plan.
                    </p>
                </div>
            </div>

            <x-card
                x-data
                @click="$dispatch('open-modal', 'create_tune')"
                is="button"
                type="button"
                class="mt-7 h-28 w-full cursor-pointer text-left transition hover:border-primary/50"
            >
                <div class="flex h-full items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-foreground">
                            What's the Tune?
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Create a tune and share it with your friends.
                        </p>
                    </div>

                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-muted text-xl text-foreground">
                        +
                    </span>
                </div>
            </x-card>
        </header>

        {{-- Success message --}}
        @if (session('success'))
            <div class="mb-5 rounded-xl border border-border bg-muted p-3 text-sm text-foreground">
                {{ session('success') }}
            </div>
        @endif

        {{-- Main filters --}}
        <section class="space-y-4">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                    Tune collection
                </p>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('tunes.index', array_filter(['status' => request('status')])) }}"
                        @class([
                            'rounded-full border px-3 py-1.5 text-sm font-medium transition',
                            'border-primary bg-primary text-primary-foreground' => $filter === 'all',
                            'border-border text-muted-foreground hover:bg-muted hover:text-foreground' => $filter !== 'all',
                        ])
                    >
                        All Tunes
                    </a>

                    <a
                        href="{{ route('tunes.index', array_filter(['filter' => 'mine', 'status' => request('status')])) }}"
                        @class([
                            'rounded-full border px-3 py-1.5 text-sm font-medium transition',
                            'border-primary bg-primary text-primary-foreground' => $filter === 'mine',
                            'border-border text-muted-foreground hover:bg-muted hover:text-foreground' => $filter !== 'mine',
                        ])
                    >
                        My Tunes
                    </a>

                    <a
                        href="{{ route('tunes.index', array_filter(['filter' => 'shared', 'status' => request('status')])) }}"
                        @class([
                            'rounded-full border px-3 py-1.5 text-sm font-medium transition',
                            'border-primary bg-primary text-primary-foreground' => $filter === 'shared',
                            'border-border text-muted-foreground hover:bg-muted hover:text-foreground' => $filter !== 'shared',
                        ])
                    >
                        Shared with Me
                    </a>
                </div>
            </div>

            {{-- Status filters --}}
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                    Filter by status
                </p>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('tunes.index', array_filter(['filter' => $filter !== 'all' ? $filter : null])) }}"
                        @class([
                            'rounded-full border px-3 py-1 text-xs font-medium transition',
                            'border-border bg-muted text-foreground' => !request()->filled('status'),
                            'border-border text-muted-foreground hover:bg-muted' => request()->filled('status'),
                        ])
                    >
                        All statuses
                    </a>

                    @foreach (App\TuneStatus::cases() as $statusOption)
                        @php
                            $statusValue = $statusOption->value;

                            $statusClasses = match ($statusValue) {
                                'pending' => 'border-amber-300 bg-amber-50 text-amber-800 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                                'in_progress' => 'border-blue-300 bg-blue-50 text-blue-800 dark:border-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
                                'completed' => 'border-green-300 bg-green-50 text-green-800 dark:border-green-700 dark:bg-green-950/40 dark:text-green-300',
                                default => 'border-border bg-muted text-foreground',
                            };

                            $isStatusActive = request('status') === $statusValue;
                        @endphp

                        <a
                            href="{{ route('tunes.index', array_filter([
                                'filter' => $filter !== 'all' ? $filter : null,
                                'status' => $statusValue,
                            ])) }}"
                            @class([
                                'rounded-full border px-3 py-1 text-xs font-medium transition',
                                $statusClasses => $isStatusActive,
                                'border-border text-muted-foreground hover:bg-muted' => !$isStatusActive,
                            ])
                        >
                            {{ $statusOption->label() }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- List heading --}}
        <div class="mb-4 mt-8 flex flex-wrap items-end justify-between gap-2">
            <div>
                <h2 class="text-xl font-semibold text-foreground">
                    @switch($filter)
                        @case('mine')
                            My Tunes
                            @break

                        @case('shared')
                            Tunes Shared with Me
                            @break

                        @default
                            All Tunes
                    @endswitch
                </h2>

                <p class="mt-1 text-sm text-muted-foreground">
                    {{ $tunes->count() }}
                    {{ $tunes->count() === 1 ? 'tune' : 'tunes' }}
                    {{ request()->filled('status') ? 'matching this status' : 'shown' }}
                </p>
            </div>
        </div>

        {{-- Tune cards --}}
        <div class="grid gap-5 md:grid-cols-2">
            @forelse ($tunes as $tune)
                @php
                    $isOwner = (int) $tune->user_id === (int) auth()->id();

                    $statusValue = $tune->status->value;

                    $statusClasses = match ($statusValue) {
                        'pending' => 'border-amber-300 bg-amber-50 text-amber-800 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                        'in_progress' => 'border-blue-300 bg-blue-50 text-blue-800 dark:border-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
                        'completed' => 'border-green-300 bg-green-50 text-green-800 dark:border-green-700 dark:bg-green-950/40 dark:text-green-300',
                        default => 'border-border bg-muted text-foreground',
                    };
                @endphp

                <x-card href="{{ route('tunes.show', $tune) }}">
                    @if ($tune->image_path)
                        <div class="-mx-4 -mt-4 mb-4 overflow-hidden rounded-t-lg">
                            <img
                                src="{{ asset('storage/' . $tune->image_path) }}"
                                alt=""
                                class="h-auto max-h-64 w-full object-cover"
                            >
                        </div>
                    @endif

                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-lg font-semibold text-foreground">
                            {{ $tune->title }}
                        </h3>

                        <span
                            @class([
                                'shrink-0 rounded-full border px-2.5 py-1 text-xs font-medium',
                                'border-border text-muted-foreground' => !$isOwner,
                                'border-primary/30 bg-primary/10 text-primary' => $isOwner,
                            ])
                        >
                            {{ $isOwner ? 'Yours' : 'Shared' }}
                        </span>
                    </div>

                    <div class="mt-3">
                        <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">
                            {{ $tune->status->label() }}
                        </span>
                    </div>

                    @if ($tune->description)
                        <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-muted-foreground">
                            {{ $tune->description }}
                        </p>
                    @endif

                    <div class="mt-5 flex items-center justify-between gap-3 border-t border-border pt-3 text-xs text-muted-foreground">
                        <span class="truncate">
                            {{ $isOwner ? 'Created by you' : 'By @' . $tune->user->username }}
                        </span>

                        <span class="shrink-0">
                            {{ $tune->created_at->diffForHumans() }}
                        </span>
                    </div>
                </x-card>
            @empty
                <div class="rounded-2xl border border-dashed border-border p-8 text-center md:col-span-2 md:p-12">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-muted text-2xl text-muted-foreground">
                        ♪
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-foreground">
                        @if ($filter === 'mine')
                            You haven't created any tunes yet.
                        @elseif ($filter === 'shared')
                            No tunes have been shared with you yet.
                        @else
                            No tunes found.
                        @endif
                    </h3>

                    <p class="mx-auto mt-2 max-w-sm text-sm text-muted-foreground">
                        @if ($filter === 'shared')
                            When a friend shares a tune with you, it'll appear here.
                        @elseif (request()->filled('status'))
                            Try a different status filter or create a new tune.
                        @else
                            Start by creating a tune to capture your thoughts.
                        @endif
                    </p>

                    @if ($filter !== 'shared')
                        <button
                            type="button"
                            class="btn mt-5"
                            x-data
                            @click="$dispatch('open-modal', 'create_tune')"
                        >
                            Create a Tune
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Create tune modal --}}
        <x-tune.modal :friends="$friends" />
    </div>
</x-layout>
