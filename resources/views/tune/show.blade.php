<x-layout>
    <div class="max-w-4xl mx-auto py-8">


        <div class="flex justify-between items-center mb-4">
            <a href="{{route('tunes.index')}}" class="flex items-center gap-2 text-sm font-medium">
                
                Back to Tunes
            </a>

            <div class="gap-x-3 flex items-center">
                <a href="" class="btn btn-outlined">Edit</a>
                <form action="{{route('tunes.destroy', $tune->id)}}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tune?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outlined hover:text-red-500">Delete</button>
                </form>
            </div>
        </div>


        <div class="mt-8 space-y-6">
            <h1 class="text-4xl font-bold">{{ $tune->title }}</h1>

            <div class="mt-2 flex gap-x-3 items-center">
                <x-tune.status-label status="{{ $tune->status }}">
                    {{ $tune->status->label() }}
                </x-tune.status-label>
                <div class="text-sm text-muted-foreground">{{ $tune->created_at->diffForHumans() }}</div>
            </div>

            <x-card>
                <div class="text-foreground prose prose-invert max-w-none cursor-pointer ">{{ $tune->description }}</div>
            </x-card>
            <div>
                <h3 class="text-lg font-semibold mb-2">Links</h3>
                <div class="flex gap-x-3 mt-2 flex-wrap space-y-2">
                    @forelse($tune->links as $link)
                        <x-card :href="$link" class="text-primary hover:text-primary/80 font-medium flex gap-x-3 items-center">
                            {{ $link }}
                        </x-card>
                    @empty
                        <p class="text-sm text-muted-foreground">No links available.</p>
                    @endforelse
                </div>

            </div>

        </div>
    
    </div>

</x-layout> 