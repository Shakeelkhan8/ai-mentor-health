@extends('backend_app.layouts.template')
@section('content')
@php
    $user = Auth::user();
@endphp
<style>
    .loading-dots { font-weight: bold; color: #007bff; }
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .coach-disclaimer {
        font-size: 0.85rem;
        background: #fff8e6;
        border: 1px solid #ffe08a;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
    }
</style>
<div class="layout-page">
    @include('backend_app.layouts.nav')
    <div class="content-wrapper">
      <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">{{ $productName }} — AI Coach</h4>
                <p class="mb-0 text-muted">Personalized wellness coaching for habits, fitness, nutrition, sleep, and stress.</p>
            </div>
        </div>

        <div class="coach-disclaimer">
            {{ $disclaimer }}
        </div>

        <div class="chat-container px-5 border rounded-3 bg-white" style="overflow-y: scroll;height:500px;" id="chat-container">
            @forelse(($conversation?->messages ?? collect()) as $message)
                @if($message->role->value === 'user')
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="float-start bg-light p-2 pe-4 rounded-3 user">
                                <span class="px-3">{{ $message->content }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row mt-3 justify-content-end">
                        <div class="col-6">
                            <div class="float-end d-flex flex-row gap-3">
                                <div class="bg-light p-2 ps-4 rounded-3 ai">
                                    <span class="me-3">{{ $message->content }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="row mt-3 justify-content-end">
                    <div class="col-6">
                        <div class="float-end d-flex flex-row gap-3">
                            <div class="bg-light p-2 ps-4 rounded-3 ai">
                                <span class="me-3">Hi {{ $user->name }} — I’m your wellness coach. What habit or goal should we work on today?</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="row position-relative px-2 mt-3">
            @if(config('mentor.features.care_marketplace'))
            <div class="col-12 mb-2">
                <form action="{{ route('search-hospitals') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary mb-2">Search Nearest Hospital</button>
                    <input id="lat" type="hidden" name="latitude">
                    <input id="long" type="hidden" name="longtitude">
                </form>
            </div>
            @endif

            <form id="coach-form" class="col-12">
                @csrf
                <input type="hidden" id="conversation_id" name="conversation_id" value="{{ $conversation?->id }}">
                <div class="input-group mb-3">
                    <input
                        type="text"
                        class="form-control"
                        name="message"
                        id="coach-message"
                        placeholder="Ask your coach about habits, sleep, fitness, nutrition, or stress..."
                        autocomplete="off"
                        maxlength="2000"
                        required
                    >
                    <button class="btn btn-primary" type="submit" id="coach-send">Send</button>
                </div>
            </form>
        </div>

        @include('backend_app.layouts.footer')
        <div class="content-backdrop fade"></div>
      </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function () {
    function escapeHtml(text) {
        return $('<div>').text(text ?? '').html();
    }

    function scrollToBottom() {
        const chatContainer = $('#chat-container');
        chatContainer.scrollTop(chatContainer[0].scrollHeight);
    }

    $(function () {
        scrollToBottom();

        @if(config('mentor.features.care_marketplace'))
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
                $('#lat').val(position.coords.latitude);
                $('#long').val(position.coords.longitude);
            });
        }
        @endif

        $('#coach-form').on('submit', function (event) {
            event.preventDefault();

            const input = $('#coach-message');
            const userInput = $.trim(input.val());
            if (!userInput) {
                return;
            }

            $('#chat-container').append(`
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="float-start bg-light p-2 pe-4 rounded-3 user">
                            <span class="px-3">${escapeHtml(userInput)}</span>
                        </div>
                    </div>
                </div>
            `);
            scrollToBottom();

            const loadingDots = $(`
                <div class="row mt-3 justify-content-end loading">
                    <div class="col-6">
                        <div class="float-end d-flex flex-row gap-3">
                            <div class="bg-light p-2 ps-4 rounded-3 ai">
                                <span class="me-3 loading-dots">...</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).css('animation', 'blink 1s step-end infinite');

            $('#chat-container').append(loadingDots);
            input.val('');
            $('#coach-send').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '{{ route('coach.messages.store') }}',
                data: {
                    message: userInput,
                    conversation_id: $('#conversation_id').val() || null,
                    _token: $('input[name="_token"]').val()
                },
                success: function (response) {
                    $('.loading').remove();
                    $('#coach-send').prop('disabled', false);

                    if (response.conversation_id) {
                        $('#conversation_id').val(response.conversation_id);
                    }

                    const coachMessage = response.message || 'No response received.';
                    $('#chat-container').append(`
                        <div class="row mt-3 justify-content-end">
                            <div class="col-6">
                                <div class="float-end d-flex flex-row gap-3">
                                    <div class="bg-light p-2 ps-4 rounded-3 ai">
                                        <span class="me-3">${escapeHtml(coachMessage)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    scrollToBottom();
                },
                error: function (xhr) {
                    $('.loading').remove();
                    $('#coach-send').prop('disabled', false);
                    const errorMessage = (xhr.responseJSON && xhr.responseJSON.error)
                        ? xhr.responseJSON.error
                        : 'An error occurred. Please try again.';
                    $('#chat-container').append(`
                        <div class="row mt-3 justify-content-end">
                            <div class="col-6">
                                <div class="bg-light p-2 ps-4 rounded-3 ai">
                                    <span class="me-3">${escapeHtml(errorMessage)}</span>
                                </div>
                            </div>
                        </div>
                    `);
                    scrollToBottom();
                }
            });
        });
    });
})();
</script>
@endsection
