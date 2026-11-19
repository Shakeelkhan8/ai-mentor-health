<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $productName }} — AI Coach</h2>
        <p class="mt-1 text-sm text-gray-600">Personalized wellness coaching for habits, fitness, nutrition, sleep, and stress.</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <x-alert type="warning">{{ $disclaimer }}</x-alert>

            @if(config('mentor.demo.enabled'))
                <x-alert type="info">
                    <strong>Demo mode:</strong> the AI is switched off in this demo, so the coach answers with
                    pre-written tips on sleep, stress, nutrition and fitness.
                </x-alert>
            @endif

            @if($dailyLimit !== null)
                <p class="text-sm text-gray-600" id="coach-usage">
                    <span id="coach-remaining">{{ $remainingToday }}</span> of {{ $dailyLimit }} free messages left today
                    · <a href="{{ route('billing.show') }}" class="font-medium text-brand-600 hover:underline">Upgrade to Pro</a> for unlimited coaching
                </p>
            @endif

            <div id="chat-container" class="h-[28rem] overflow-y-auto rounded-lg border border-gray-200 bg-white p-4 space-y-3" aria-live="polite">
                @forelse(($conversation?->messages ?? collect()) as $message)
                    @if($message->role->value === 'user')
                        <div class="flex justify-end"><p class="max-w-[75%] whitespace-pre-line rounded-lg bg-brand-600 px-3 py-2 text-sm text-white">{{ $message->content }}</p></div>
                    @else
                        <div class="flex justify-start"><p class="max-w-[75%] whitespace-pre-line rounded-lg bg-gray-100 px-3 py-2 text-sm text-gray-900">{{ $message->content }}</p></div>
                    @endif
                @empty
                    <div class="flex justify-start">
                        <p class="max-w-[75%] rounded-lg bg-gray-100 px-3 py-2 text-sm text-gray-900">Hi {{ Auth::user()->name }} — I’m your wellness coach. What habit or goal should we work on today?</p>
                    </div>
                @endforelse
            </div>

            <form id="coach-form" class="flex gap-2">
                <input type="hidden" id="conversation_id" value="{{ $conversation?->id }}">
                <label for="coach-message" class="sr-only">Message</label>
                <x-text-input
                    id="coach-message"
                    class="flex-1"
                    placeholder="Ask your coach about habits, sleep, fitness, nutrition, or stress..."
                    autocomplete="off"
                    maxlength="2000"
                    required
                />
                <x-primary-button id="coach-send">Send</x-primary-button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const chat = document.getElementById('chat-container');
            const form = document.getElementById('coach-form');
            const input = document.getElementById('coach-message');
            const send = document.getElementById('coach-send');
            const conversationId = document.getElementById('conversation_id');
            const remaining = document.getElementById('coach-remaining');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            // textContent, never innerHTML: coach replies and user input are untrusted text.
            function bubble(text, fromUser) {
                const row = document.createElement('div');
                row.className = 'flex ' + (fromUser ? 'justify-end' : 'justify-start');
                const p = document.createElement('p');
                p.className = 'max-w-[75%] whitespace-pre-line rounded-lg px-3 py-2 text-sm '
                    + (fromUser ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-900');
                p.textContent = text;
                row.appendChild(p);
                chat.appendChild(row);
                chat.scrollTop = chat.scrollHeight;
                return row;
            }

            chat.scrollTop = chat.scrollHeight;

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                const text = input.value.trim();
                if (!text) {
                    return;
                }

                bubble(text, true);
                const pending = bubble('…', false);
                input.value = '';
                send.disabled = true;

                let reply;
                try {
                    const response = await fetch(@json(route('coach.messages.store')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ message: text, conversation_id: conversationId.value || null }),
                    });
                    const data = await response.json().catch(() => ({}));

                    if (response.ok) {
                        if (data.conversation_id) {
                            conversationId.value = data.conversation_id;
                        }
                        if (remaining && typeof data.remaining_today === 'number') {
                            remaining.textContent = data.remaining_today;
                        }
                        reply = data.message || 'No response received.';
                    } else {
                        reply = data.error || data.message || 'An error occurred. Please try again.';
                    }
                } catch (error) {
                    reply = 'An error occurred. Please try again.';
                }

                pending.remove();
                bubble(reply, false);
                send.disabled = false;
                input.focus();
            });
        })();
    </script>
</x-app-layout>
