@props(['dark' => false, 'href' => null])

{{-- The brand lockup: mark plus wordmark. `dark` switches the wordmark colours for dark backgrounds. --}}
@php
    $name = config('mentor.name');
    $lead = str_ends_with($name, ' Health') ? substr($name, 0, -7) : $name;
    $tail = str_ends_with($name, ' Health') ? 'Health' : null;
@endphp

<{{ $href ? 'a' : 'span' }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <x-application-logo class="h-8 w-8" />
    <span class="text-[1.05rem] font-semibold tracking-tight {{ $dark ? 'text-white' : 'text-gray-900' }}">
        {{ $lead }}@if ($tail) <span class="{{ $dark ? 'text-brand-300' : 'text-brand-600' }}">{{ $tail }}</span>@endif
    </span>
</{{ $href ? 'a' : 'span' }}>
