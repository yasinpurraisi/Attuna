@props([
    'tune' => new App\Models\Tune()
])
<x-layout.model name="{{ $tune->exists ? 'edit_tune' : 'create_tune' }}" title="{{ $tune->exists ? 'Edit Tune' : 'Create Tune' }}">
    <form 
    x-data="{
        status: @js(old('status', $tune->status->value)),
        newLink: '',
        links: @js(old('links', $tune->links?->getArrayCopy() ?? [])),
        newTask: '',
        tasks: @js(old('tasks', $tune->tasks->map->only(['id', 'description', 'completed']))),
    }" 
    method="POST" 
    action="{{ $tune->exists ? route('tunes.update', $tune) :   route('tunes.store') }}"
    enctype="multipart/form-data">
        @csrf

        @if ($tune->exists)
            @method('PATCH')
        @endif
            <div class="space-y-6">
            <x-form.field
                label="Title"
                name="title"
                type="text"
                placeholder="Enter an Tune for title"
                required
                autofocus
                value="{{ $tune->title }}"
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
                value="{{ $tune->description }}"
            />  
            {{-- -----------Image--------- --}}
            <div class="space-y-2">
                <label for="image" class="label">Featured Image</label>
                @if ($tune->image_path)
                    <div class="space-y-2">
                        <img src="{{ asset('storage/'. $tune->image_path) }}" alt="" class="w-full h-auto object-cover rounded-lg">
                        <button form="delete-image-form" class="btn btn-outlined h-10 w-full">Remove Image</button>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"/>
                <x-form.error name="image" />
            </div>
            {{-- ---------Task---------------- --}}
            <div>   

                <fieldset class="space-y-3">

                    <legend class="label">Tasks</legend>

                    <template x-for="(task, index) in tasks" :key="task.id || index">

                        <div class="flex gap-x-2 items-center">

                            <input class="input" :name="`tasks[${index}][description]`" x-model="tasks[index].description">
                            <input type="hidden" :name="`tasks[${index}][completed]`" :value="task.completed ? 1 : 0">

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
                        @click="tasks.push({ description: newTask, completed: false}); newTask='' "
                        :disabled="newTask.trim().length === 0">+</button>

                    </div>

                </fieldset>

            </div>

            {{-- --------Links HERE------ --}}
            <div>   

                <fieldset class="space-y-3">

                    <legend class="label">Links</legend>

                    <template x-for="(link, index) in links" :key="index">

                        <div class="flex gap-x-2 items-center">

                            <input class="input" type="url" name="links[]" x-model="links[index]">

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
                    <button type="submit" class="btn">{{ $tune->exists ? 'Update' : 'Create'}}</button>   
                </div>                                   
            </div>

    </form>
    {{-- -------------Delete Image------------ --}}
    @if ($tune->image_path)
        <form method="POST" action="{{ route('tunes.image.destroy', $tune) }}" id="delete-image-form">
            @csrf
            @method('DELETE')

        </form>
    @endif

</x-layout.model>