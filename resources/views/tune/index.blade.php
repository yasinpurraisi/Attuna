
<x-layout>

    <div class="text-muted-foreground">

        <header class="py-8 md:py-12">

            <h1 class="text-3xl font-bold">Ideas</h1>

            <p class="text-muted-foreground text-sm mt-2">
                Capture your thoughts. Make a plan.
            </p>

            <x-card
                x-data
                @click="$dispatch('open-modal', 'create_tune')"
                is="button"
                type="button"
                class="mt-10 cursor-pointer h-32 w-full"
            >
                <p>What's the Tune?</p>
            </x-card>

        </header>

        <div>

            <a
                href="/tunes"
                class="btn {{ request()->has('status') ? 'btn-outlined' : '' }}"
            >
                All
            </a>

            @foreach (App\TuneStatus::cases() as $status)

                <a
                    href="/tunes?status={{ $status->value }}"
                    class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}"
                >
                    {{ $status->label() }}

                    <span class="ml-1 text-muted-foreground">
                        ({{ Auth::user()->tunes()->where('status', $status)->count() }})
                    </span>
                </a>

            @endforeach

        </div>

        <div class="mt-10">

            <div class="grid md:grid-cols-2 gap-6">

                @forelse ($tunes as $tune)

                    <x-card href="{{ route('tunes.show', $tune->id) }}">
                        @if ($tune->image_path)
                            <div class="mb-4 -mx-4 -mt-4 rounded-t-lg overflow-hidden">
                                <img src="{{ asset('storage/'. $tune->image_path) }}" alt="" class="w-full h-auto object-cover">
                            </div>
                        @endif

                        <h3 class="text-foreground text-lg">
                            {{ $tune->title }}
                        </h3>

                        <x-tune.status-label status="{{ $tune->status }}">
                            {{ $tune->status->label() }}
                        </x-tune.status-label>

                        <div class="mt-5 line-clamp-3">
                            {{ $tune->description }}
                        </div>

                        <div class="mt-4">
                            {{ $tune->created_at->diffForHumans() }}
                        </div>

                    </x-card>

                @empty

                    <h2 class="text-4xl font-bold text-purple-600">
                        No ideas at this time
                    </h2>

                @endforelse

            </div>

        </div>

        {{-- -------------------Modal------------------------ --}}
        <x-tune.modal />
    </div>

</x-layout>

