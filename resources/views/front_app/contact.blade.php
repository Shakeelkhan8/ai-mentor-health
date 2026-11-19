<x-marketing-layout title="Contact">
    <section class="relative overflow-hidden bg-ink">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50rem_30rem_at_20%_-20%,rgba(16,185,133,0.22),transparent_65%)]"></div>
        <div class="relative mx-auto max-w-6xl px-6 py-20">
            <p class="text-sm font-semibold text-brand-300">Contact</p>
            <h1 class="mt-2 max-w-2xl text-4xl font-semibold tracking-tight text-white">Questions about AI Mentor Health? We'd like to hear them.</h1>
            <p class="mt-4 max-w-xl text-gray-300">Send a message and we'll reply by email.</p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-2xl px-6">
            <form action="{{ route('contact.store') }}" method="POST" class="space-y-6 rounded-2xl border border-gray-200 p-8 shadow-sm">
                <div class="grid gap-6 sm:grid-cols-2">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Phone" />
                        <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone')" required autocomplete="tel" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="message" value="Message" />
                    <textarea id="message" name="message" rows="6" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>

                <button type="submit" class="btn-primary w-full py-3">Send message</button>
            </form>
        </div>
    </section>
</x-marketing-layout>
