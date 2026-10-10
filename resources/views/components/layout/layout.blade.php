@props([
    'title' => 'Attuna'
])

<!DOCTYPE html>
<html lang="eng">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-foreground">
  
  <header>
    <x-layout.nav />
  </header>

  <main class="max-w-7xl mx-auto px-6 pb-18">
    {{ $slot }}
  </main>
  <div>
    @session('success')
      <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow"
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition.opacity.duration.500ms>
        {{ session('success') }}
      </div>
    @endsession
  </div>
  <footer>
    © 2026 Attuna
  </footer>

</body>
</html>