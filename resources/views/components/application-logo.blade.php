{{-- The brand mark on its own. Decorative by default: pair it with the product name, or pass an alt. --}}
<img src="{{ asset('images/brand/logo-mark.svg') }}" alt="" width="36" height="36" {{ $attributes->merge(['class' => 'h-9 w-9 shrink-0']) }}>
