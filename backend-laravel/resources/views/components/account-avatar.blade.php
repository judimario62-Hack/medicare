@props([
    'name' => 'Utilisateur',
    'path' => null,
    'size' => 44,
])

@php
    $initials = collect(preg_split('/\s+/', trim((string) $name)))
        ->filter()
        ->take(2)
        ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');
@endphp
<style>
    .account-avatar {
        width: var(--avatar-size, 44px);
        height: var(--avatar-size, 44px);
        display: inline-grid;
        place-items: center;
        flex: 0 0 var(--avatar-size, 44px);
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(135deg, #1d4ed8, #7c3aed);
        color: #fff;
        font-size: calc(var(--avatar-size, 44px) * 0.34);
        font-weight: 800;
        box-shadow: 0 8px 18px rgba(15,23,42,.14);
        vertical-align: middle;
    }
    .account-avatar img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }
</style>
<span
    {{ $attributes->merge(['class' => 'account-avatar']) }}
    style="--avatar-size: {{ (int) $size }}px;"
    aria-label="{{ $name }}"
>
    @if ($path)
        <img src="{{ asset('storage/'.$path) }}" alt="{{ $name }}" loading="lazy">
    @else
        {{ $initials ?: 'U' }}
    @endif
</span>
