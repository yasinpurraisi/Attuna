<x-layout>
    <x-form title="Register an account" description="Start Tunning With Your Team.">
        <form action="/register" method="POST" class="mt-10 space-y-4">
            @csrf
            <x-form.field name="name" label="Name" type="text" required />
            <x-form.field name="username" label="Username" type="text" required />
            <x-form.field name="password" label="Password" type="password" required />
            <button type="submit" class="btn btn-primary w-full">Register</button>
        </form>
    </x-form>
</x-layout>
