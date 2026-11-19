<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <h2 class="text-xl font-semibold text-gray-900">Checkout canceled</h2>
                <p class="mt-2 text-gray-600">No charge was made. You can start again whenever you’re ready.</p>
                <a href="{{ route('billing.show') }}" class="mt-4 btn-primary">Back to Billing</a>
            </x-card>
        </div>
    </div>
</x-app-layout>
