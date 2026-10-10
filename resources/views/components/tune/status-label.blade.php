@props(['status' => 'pending'])

@php
    $classes = "inline-block text-xs rounded-full border mt-2 px-2 p-y-1 text-sx font-medium";

    if ($status === 'pending') {
        $classes .= ' bg-yellow-500/10 border-yellow-500/20 text-yellow-500';
    } elseif ($status === 'in_progress') {
        $classes .= ' bg-blue-500/10 border-blue-500/20 text-blue-500';
    } elseif ($status === 'completed') {
        $classes .= ' bg-primary/10 border-primary/20 text-primary';
    }
@endphp




<span {{ $attributes(['class' => $classes]) }}>
    {{ $slot }}
</span>
