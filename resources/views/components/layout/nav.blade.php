
<nav class="border-b border-border px-6">
    <div class="max-w-7xl mx-auto h-16 flex items-center justify-between">

        {{-- Logo --}}
        <div>
            <a href="/" class="text-2xl font-bold text-foreground hover:text-primary transition-colors">
                Attuna
            </a>
        </div>

        <div class="flex items-center gap-x-5">
            @auth
                {{-- Profile Dropdown --}}
                <div class="relative" id="profile-dropdown">

                    {{-- Avatar Button --}}
                    <button
                        type="button"
                        id="profile-button"
                        class="flex items-center gap-2 rounded-full p-1 hover:bg-muted transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500"
                        aria-expanded="false"
                        aria-controls="profile-menu"
                        aria-label="Open profile menu"
                    >
                        <img
                            src="{{ asset('images/avatars/' . Auth::user()->avatar) }}"
                            alt="Profile picture"
                            class="w-10 h-10 rounded-full object-cover border border-border"
                        >

                        <svg
                            id="profile-arrow"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 text-muted-foreground transition-transform duration-200"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m6 9 6 6 6-6"
                            />
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div
                        id="profile-menu"
                        class="hidden absolute right-0 top-full mt-3 w-52 p-2 bg-card text-card-foreground border border-border rounded-xl shadow-lg z-50"
                    >
                        {{-- User Information --}}
                        <div class="px-3 py-2 mb-1">
                            <p class="text-sm font-semibold text-foreground truncate">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-xs text-muted-foreground truncate mt-1">
                                {{ '@'.Auth::user()->username }}
                            </p>
                        </div>

                        <div class="border-t border-border my-1"></div>

                        {{-- Profile Link --}}
                        <a
                            href="/profile"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-foreground hover:bg-muted transition-colors"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.1a7.5 7.5 0 0 1 15 0A17.9 17.9 0 0 1 12 21.75c-2.7 0-5.25-.6-7.5-1.65Z"
                                />
                            </svg>

                            <span>Profile</span>
                        </a>

                        {{-- Logout --}}
                        <form method="POST" action="/logout">
                            @csrf

                            <button
                                type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-muted-foreground hover:text-red-600 hover:bg-red-500/10 transition-colors"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M18 15l3-3m0 0-3-3m3 3H9"
                                    />
                                </svg>

                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            @guest
                <a
                    href="/login"
                    class="text-sm text-muted-foreground hover:text-foreground transition-colors"
                >
                    Sign In
                </a>

                <a href="/register" class="btn">
                    Register
                </a>
            @endguest
        </div>
    </div>
</nav>

{{-- Dropdown JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('profile-button');
        const menu = document.getElementById('profile-menu');
        const dropdown = document.getElementById('profile-dropdown');
        const arrow = document.getElementById('profile-arrow');

        if (!button || !menu || !dropdown) return;

        function closeMenu() {
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');

            if (arrow) {
                arrow.classList.remove('rotate-180');
            }
        }

        function toggleMenu() {
            const isOpen = !menu.classList.contains('hidden');

            if (isOpen) {
                closeMenu();
            } else {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');

                if (arrow) {
                    arrow.classList.add('rotate-180');
                }
            }
        }

        button.addEventListener('click', toggleMenu);

        document.addEventListener('click', function (event) {
            if (!dropdown.contains(event.target)) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
                button.focus();
            }
        });
    });
</script>

