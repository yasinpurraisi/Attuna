
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
        <x-layout.model name="create_tune" title="New Tune">
            <form 
            x-data="{
                status: 'pending',
                newLink: '',
                links: [],
                newTask: '',
                tasks: [],
            }" 
            method="POST" 
            action="{{ route('tunes.store') }}">
                @csrf
                  <div class="space-y-6">
                    <x-form.field
                        label="Title"
                        name="title"
                        type="text"
                        placeholder="Enter an Tune for title"
                        required
                        autofocus
                    />

                    <div class="space-y-2">
                        <label for="status" class="label">Status</label>

                        <div class="flex gap-x-3">
                            @foreach (App\TuneStatus::cases() as $status )
                                <button 
                                type="button" 
                                @click="status = @js($status->value)"
                                class="btn  flex-1 h-10" 
                                :class="status === @js($status->value) ? '' : 'btn-outlined' "
                            >
                                {{ $status->label() }}</button>
                            @endforeach
                            <input type="hidden" name="status" :value="status">
                        </div>

                        <x-form.error name="status" />
                    </div>

                    <x-form.field
                        label="Description"
                        name="description"
                        type="textarea"
                        placeholder="Describe your tune..."
                    />  
                    {{-- ---------Task---------------- --}}
                    <div>   

                        <fieldset class="space-y-3">

                            <legend class="label">Tasks</legend>

                            <template x-for="(task, index) in tasks">

                                <div class="flex gap-x-2 items-center">

                                    <input class="input" name="tasks[]" x-model="task">

                                    <button 
                                    type="button" 
                                    class="text-4xl" 
                                    @click="tasks.splice(index, 1);"
                                    >×</button>

                                </div>

                            </template>


                            <div class="flex gap-x-2 items-center">

                                <input 
                                x-model="newTask"
                                id="new-task" 
                                placeholder="Add a Task..." 
                                class="input flex-1"
                                spellcheck="false"
                                />

                                <button 
                                type="button" 
                                class="text-4xl" 
                                @click="tasks.push(newTask); newTask='' "
                                :disabled="newTask.trim().length === 0">+</button>

                            </div>

                        </fieldset>

                    </div>

                    {{-- --------Links HERE------ --}}
                    <div>   

                        <fieldset class="space-y-3">

                            <legend class="label">Links</legend>

                            <template x-for="(link, index) in links">

                                <div class="flex gap-x-2 items-center">

                                    <input class="input" name="links[]" x-model="link">

                                    <button 
                                    type="button" 
                                    class="text-4xl" 
                                    @click="links.splice(index, 1);"
                                    >×</button>

                                </div>

                            </template>


                            <div class="flex gap-x-2 items-center">

                                <input 
                                x-model="newLink"
                                type="url" 
                                id="new-link" 
                                placeholder="http://example.com" 
                                autocomplete="url"
                                class="input flex-1"
                                spellcheck="false"
                                />

                                <button 
                                type="button" 
                                class="text-4xl" 
                                @click="links.push(newLink.trim()); newLink='' "
                                :disabled="newLink.trim().length === 0">+</button>

                            </div>

                        </fieldset>

                    </div>
                    <div class="flex justify-end gap-x-5">
                        <button type="button" @click="$dispatch('close-modal')">Cancel</button>  
                        <button type="submit" class="btn">Create</button>   
                    </div>                                   
                  </div>



            </form>
        </x-layout.model>
    </div>

</x-layout>

