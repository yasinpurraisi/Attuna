
<x-layout.model name="edit_profile" title="Edit Profile">
    <div
        x-data="{
            avatar: @js(old('avatar', Auth::user()->avatar))
        }"
        class="space-y-6"
    >
        {{-- Update Profile Form --}}
        <form
            method="POST"
            action="{{ route('profile.update') }}"
        >
            @csrf
            @method('PATCH')

            <div class="space-y-6">

                {{-- Avatar Picker --}}
                <div class="space-y-3">
                    <label class="label">Profile Picture</label>

                    {{-- Current Selection Preview --}}
                    <div class="flex items-center gap-4">
                        <img
                            :src="'{{ asset('images/avatars') }}/' + avatar"
                            alt="Selected avatar"
                            class="w-20 h-20 rounded-full object-cover border-2 border-purple-500"
                        >

                        <div>
                            <p class="text-foreground font-medium">
                                Choose your avatar
                            </p>
                            <p class="text-sm text-muted-foreground mt-1">
                                Pick the one that feels like you.
                            </p>
                        </div>
                    </div>

                    {{-- Available Avatars --}}
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 pt-3">
                        @foreach (range(1, 29) as $number)
                            @php
                                $avatarName = $number . '.svg';
                            @endphp

                            <button
                                type="button"
                                @click="avatar = @js($avatarName)"
                                :class="avatar === @js($avatarName)
                                    ? 'border-purple-500 ring-2 ring-purple-500/30 bg-purple-500/10'
                                    : 'border-border hover:border-purple-400'"
                                class="aspect-square rounded-xl border-2 p-1.5 transition-all duration-150"
                                aria-label="Choose avatar {{ $number }}"
                                :aria-pressed="avatar === @js($avatarName)"
                            >
                                <img
                                    src="{{ asset('images/avatars/' . $avatarName) }}"
                                    alt=""
                                    class="w-full h-full rounded-lg object-cover"
                                    loading="lazy"
                                >
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="avatar" x-model="avatar">

                    <x-form.error name="avatar" />
                </div>

                <div class="border-t border-border"></div>

                {{-- Name --}}
                <x-form.field
                    name="name"
                    label="Name"
                    type="text"
                    value="{{ old('name', Auth::user()->name) }}"
                    placeholder="Your name"
                    required
                />

                {{-- Username --}}
                <x-form.field
                    name="username"
                    label="Username"
                    type="text"
                    value="{{ old('username', Auth::user()->username) }}"
                    placeholder="Choose a username"
                    required
                />

                {{-- Actions --}}
                <div class="flex justify-end gap-4 pt-2">
                    <button
                        type="button"
                        @click="$dispatch('close-modal')"
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn">
                        Save Changes
                    </button>
                </div>

            </div>
        </form>

        {{-- Delete Account Section --}}
        <div class="border-t border-border pt-5">
            <h3 class="font-semibold text-foreground">
                Delete Account
            </h3>

            <p class="text-sm text-muted-foreground mt-2">
                Permanently delete your account and its associated data.
                This action cannot be undone.
            </p>

            <form
                method="POST"
                action="{{ route('profile.destroy') }}"
                class="mt-4 space-y-3"
                x-data="{ confirming: false }"
            >
                @csrf
                @method('DELETE')

                <button
                    type="button"
                    @click="confirming = !confirming"
                    class="text-sm text-red-600 hover:text-red-700 transition-colors"
                >
                    <span x-show="!confirming">
                        Delete my account
                    </span>
                    <span x-show="confirming" x-cloak>
                        Cancel deletion
                    </span>
                </button>

                <div x-show="confirming" x-cloak class="space-y-3">
                    <label
                        for="delete-password"
                        class="label"
                    >
                        Enter your current password to confirm
                    </label>

                    <input
                        id="delete-password"
                        type="password"
                        name="password"
                        class="input w-full"
                        autocomplete="current-password"
                        :required="confirming"
                    >

                    <x-form.error name="password" />

                    <button
                        type="submit"
                        class="btn bg-red-600 text-white hover:bg-red-700"
                    >
                        Permanently Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout.model>
