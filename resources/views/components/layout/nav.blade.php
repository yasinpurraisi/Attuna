<nav class="border-b border-border px-6">

    <div class="max-w-7xl mx-auto h-16 flex items-center justify-between">

        <div>
            <a href="/">
                <img src="/images/tuna.png" alt="" width="100">
            </a>
        </div>

        <div class="flex gap-x-5 item_center">
            @auth
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @endauth
            @guest
                <a href="/login">Sign In</a>
                <a href="/register" class="btn">Register</a>
            @endguest
        </div>
    
    </div>

</nav>