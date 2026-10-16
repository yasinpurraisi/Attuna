
@props([
    'title' => 'Attuna'
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-background text-foreground">

    <header>
        <x-layout.nav />
    </header>

    <main class="flex-1 w-full max-w-7xl mx-auto px-6 pb-18">
        {{ $slot }}
    </main>

    {{-- Success notification --}}
    <div>
        @session('success')
            <div
                class="fixed bottom-4 right-4 z-50 rounded-xl border border-green-200/50 bg-green-600 px-5 py-3 text-sm font-medium text-white shadow-lg"
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition.opacity.duration.500ms
                role="status"
            >
                {{ session('success') }}
            </div>
        @endsession
    </div>

    {{-- Footer --}}
    <footer class="mt-16 border-t border-border/60 bg-background">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 py-7 sm:flex-row">

            {{-- Brand --}}
            <a
                href="{{ url('/') }}"
                class="flex items-center gap-2 text-sm font-semibold tracking-tight transition-opacity hover:opacity-70"
            >
                {{-- <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary text-primary-foreground">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-4 w-4"
                        aria-hidden="true"
                    >
                        <path d="M12 3v18"/>
                        <path d="M7 8c0-2 2-3 5-3s5 1 5 3-2 3-5 3-5 1-5 3 2 3 5 3 5-1 5-3"/>
                    </svg>
                </span> --}}

                <span>Attuna</span>
            </a>

            {{-- Copyright --}}
            <p class="text-center text-xs text-muted-foreground">
                &copy; {{ date('Y') }} Attuna. Made with love.
            </p>

            {{-- Back to top --}}
            <a
                href="#top"
                onclick="window.scrollTo({ top: 0, behavior: 'smooth' }); return false;"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
            >
                Back to top

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-4 w-4"
                    aria-hidden="true"
                >
                    <path d="m18 15-6-6-6 6"/>
                </svg>
            </a>

        </div>
    </footer>

</body>
</html>
