<x-layout>
    <div class="text-muted-foreground">
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2"> Capture yor thoughts. Make a plan.</p>
        </header>
        <div class="mt-10">
            <div class="grid md:grid-cols-2 gap-6">
                @forelse($tunes as $tune)
                    <x-card href="{{route('tunes.show', $tune->id)}}">
                        <h3 class="text-foreground text-lg">{{$tune->title}}</h3>
                        <x-tune.status-label status="{{ $tune->status }}">
                            {{ $tune->status->label() }}
                        </x-tune.status-label>
                        <div class="mt-5 line-clamp-3">{{ $tune->description }}</div>
                        <div class="mt-4">{{ $tune->created_at->diffForHumans() }}</div>
                    </x-card>
                @empty
                    <h2 class="text-4xl font-bold text-purple-600 ">No ideas at this time</h2>
                @endforelse
            </div>
        </div>
    </div>
</x-layout>
