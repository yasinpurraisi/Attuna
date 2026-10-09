@props([
    'title' => 'Biscuit'
])

<!DOCTYPE html>
<html lang="eng">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
  @vite(['resources\css\app.css'])
</head>

<body class="bg-background text-foreground">
  
  <header>
    <x-layout.nav />
  </header>

  <main class="max-w-7xl mx-auto px-6 pb-18">
    {{ $slot }}
  </main>

  <footer>
    © 2026 Attuna
  </footer>

</body>
</html>