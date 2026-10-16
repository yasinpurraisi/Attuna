
@props([
    'tune' => new App\Models\Tune(),
    'friends' => [],
])

@php
    $selectedFriends = old(
        'shared_with',
        $tune->exists
            ? $tune->sharedWith->pluck('id')->all()
            : []
    );

    $selectedFriends = array_map(
        'strval',
        is_array($selectedFriends) ? $selectedFriends : []
    );
@endphp

<x-layout.model
    name="{{ $tune->exists ? 'edit_tune' : 'create_tune' }}"
    title="{{ $tune->exists ? 'Edit Tune' : 'Create Tune' }}"
>
    <form
        x-data="{
            status: @js(old('status', $tune->status->value)),
            newLink: '',
            links: @js(old('links', $tune->links?->getArrayCopy() ?? [])),
            newTask: '',
            tasks: @js(old('tasks', $tune->tasks->map->only(['id', 'description', 'completed']))),
            friendSearch: '',
            selectedFriends: @js($selectedFriends),
        }"
        method="POST"
        action="{{ $tune->exists ? route('tunes.update', $tune) : route('tunes.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        @if ($tune->exists)
            @method('PATCH')
        @endif

        <div class="space-y-6">

            {{-- Title --}}
            <x-form.field
                label="Title"
                name="title"
                type="text"
                placeholder="Enter a title for your tune"
                required
                autofocus
                value="{{ old('title', $tune->title) }}"
            />

            {{-- Status --}}
            <div class="space-y-2">
                <label for="status" class="label">Status</label>

                <div class="flex gap-x-3">
                    @foreach (App\TuneStatus::cases() as $statusOption)
                        <button
                            type="button"
                            @click="status = @js($statusOption->value)"
                            class="btn flex-1 h-10"
                            :class="status === @js($statusOption->value) ? '' : 'btn-outlined'"
                        >
                            {{ $statusOption->label() }}
                        </button>
                    @endforeach

                    <input type="hidden" name="status" :value="status">
                </div>

                <x-form.error name="status" />
            </div>

            {{-- Description --}}
            <x-form.field
                label="Description"
                name="description"
                type="textarea"
                placeholder="Describe your tune..."
                value="{{ old('description', $tune->description) }}"
            />

            {{-- Featured image --}}
            <div class="space-y-2">
                <label for="image" class="label">Featured Image</label>

                @if ($tune->image_path)
                    <div class="space-y-2">
                        <img
                            src="{{ asset('storage/' . $tune->image_path) }}"
                            alt=""
                            class="w-full h-auto object-cover rounded-lg"
                        >

                        <button
                            type="submit"
                            form="delete-image-form"
                            class="btn btn-outlined h-10 w-full"
                        >
                            Remove Image
                        </button>
                    </div>
                @endif

                <input
                    id="image"
                    type="file"
                    name="image"
                    accept="image/*"
                    class="block w-full text-sm"
                >

                <x-form.error name="image" />
            </div>

            {{-- Tasks --}}
            <fieldset class="space-y-3">
                <legend class="label">Tasks</legend>

                <template x-for="(task, index) in tasks" :key="task.id || index">
                    <div class="flex gap-x-2 items-center">
                        <input
                            class="input flex-1"
                            :name="`tasks[${index}][description]`"
                            x-model="tasks[index].description"
                            required
                        >

                        <input
                            type="hidden"
                            :name="`tasks[${index}][completed]`"
                            :value="task.completed ? 1 : 0"
                        >

                        <button
                            type="button"
                            class="text-3xl"
                            aria-label="Remove task"
                            @click="tasks.splice(index, 1)"
                        >
                            ×
                        </button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input
                        x-model="newTask"
                        id="new-task"
                        placeholder="Add a task..."
                        class="input flex-1"
                        spellcheck="false"
                        @keydown.enter.prevent="
                            if (newTask.trim()) {
                                tasks.push({
                                    description: newTask.trim(),
                                    completed: false
                                });
                                newTask = '';
                            }
                        "
                    >

                    <button
                        type="button"
                        class="text-3xl"
                        @click="
                            if (newTask.trim()) {
                                tasks.push({
                                    description: newTask.trim(),
                                    completed: false
                                });
                                newTask = '';
                            }
                        "
                        :disabled="newTask.trim().length === 0"
                    >
                        +
                    </button>
                </div>

                <x-form.error name="tasks" />
            </fieldset>

            {{-- Links --}}
            <fieldset class="space-y-3">
                <legend class="label">Links</legend>

                <template x-for="(link, index) in links" :key="index">
                    <div class="flex gap-x-2 items-center">
                        <input
                            class="input flex-1"
                            type="url"
                            :name="`links[${index}]`"
                            x-model="links[index]"
                        >

                        <button
                            type="button"
                            class="text-3xl"
                            aria-label="Remove link"
                            @click="links.splice(index, 1)"
                        >
                            ×
                        </button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input
                        x-model="newLink"
                        type="url"
                        id="new-link"
                        placeholder="https://example.com"
                        autocomplete="url"
                        class="input flex-1"
                        spellcheck="false"
                    >

                    <button
                        type="button"
                        class="text-3xl"
                        @click="
                            if (newLink.trim()) {
                                links.push(newLink.trim());
                                newLink = '';
                            }
                        "
                        :disabled="newLink.trim().length === 0"
                    >
                        +
                    </button>
                </div>

                <x-form.error name="links" />
            </fieldset>

            {{-- Share with friends --}}
            <section class="space-y-3 border border-border rounded-xl p-4">
                <div>
                    <h3 class="font-semibold text-foreground">
                        Share with friends
                    </h3>

                    <p class="text-sm text-muted-foreground mt-1">
                        Choose which accepted friends can view this tune.
                    </p>
                </div>

                @if ($tune->exists && $tune->sharedWith->isNotEmpty())
                    <p class="text-xs text-muted-foreground">
                        Currently shared with {{ $tune->sharedWith->count() }}
                        {{ $tune->sharedWith->count() === 1 ? 'friend' : 'friends' }}.
                    </p>
                @endif

                @forelse ($friends as $friend)
                    <label
                        class="flex items-center gap-3 rounded-lg border border-border p-3 cursor-pointer hover:bg-muted transition"
                    >
                        <input
                            type="checkbox"
                            name="shared_with[]"
                            value="{{ $friend->id }}"
                            x-model="selectedFriends"
                            class="size-4 accent-current"
                        >

                        <img
                            src="{{ asset('images/avatars/' . $friend->avatar) }}"
                            alt=""
                            class="size-10 rounded-full object-cover"
                        >

                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-medium text-foreground">
                                {{ $friend->name }}
                            </span>

                            <span class="block text-xs text-muted-foreground">
                                {{ '@' . $friend->username }}
                            </span>
                        </span>

                        <span
                            x-show="selectedFriends.includes('{{ $friend->id }}')"
                            class="text-sm text-muted-foreground"
                        >
                            Selected
                        </span>
                    </label>
                @empty
                    <div class="rounded-lg bg-muted p-4">
                        <p class="text-sm text-muted-foreground">
                            You don't have any accepted friends to share with yet.
                        </p>

                        <a
                            href="{{ route('users.index') }}"
                            class="text-sm font-medium text-primary underline mt-2 inline-block"
                        >
                            Find people to add
                        </a>
                    </div>
                @endforelse

                <p class="text-sm text-muted-foreground">
                    <span x-text="selectedFriends.length"></span>
                    selected
                </p>

                <x-form.error name="shared_with" />
            </section>

            {{-- Actions --}}
            <div class="flex justify-end gap-x-5">
                <button
                    type="button"
                    @click="$dispatch('close-modal')"
                >
                    Cancel
                </button>

                <button type="submit" class="btn">
                    {{ $tune->exists ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </form>

    {{-- Delete image --}}
    @if ($tune->image_path)
        <form
            method="POST"
            action="{{ route('tunes.image.destroy', $tune) }}"
            id="delete-image-form"
        >
            @csrf
            @method('DELETE')
        </form>
    @endif
</x-layout.model>
