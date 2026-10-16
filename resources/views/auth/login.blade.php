<x-layout>
    <x-form title="Login to your account" description="Sign in to manage your team.">
        <form action="/login" method="POST" class="mt-10 space-y-4">
            @csrf
            <x-form.field name="username" label="Username" type="text" required />
            <x-form.field name="password" label="Password" type="password" required />
            <button type="submit" class="btn btn-primary w-full">Login</button>
        </form>
    </x-form>
</x-layout>
