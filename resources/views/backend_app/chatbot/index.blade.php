@extends('backend_app.layouts.template')
@section('content')
@php
    $user=Auth::user();
@endphp
<style>
    .loading-dots {
        font-weight: bold;
        color: #007bff; /* Adjust color as needed */
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }
</style>
<div class="layout-page">
    <!-- Navbar -->

    @include('backend_app.layouts.nav')
    <!-- / Navbar -->
    <!-- Content wrapper -->
    <div class="content-wrapper">
      <div class="container-xxl flex-grow-1 container-p-y">
        <div class="chat-container px-5" style="overflow-y: scroll;height:500px;" id="chat-container">
            <div class="row mt-3  justify-content-end">
                
                <div class="col-6 ">
                    
                    <div class="float-end d-flex flex-row gap-3">
                        <div class="bg-light p-2 ps-4 rounded-3 ai">
                        <span class="me-3">Hey, how can I help?</span>
                    </div>
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSYLklAvVDg8Ri2ZY6qxje0VJOeELO0g8I4Rg&s" class="rounded-circle" style="height: 50px;" alt="">
                    </div>
                    
                </div>
            </div>
    
        </div>
<div class="row position-relative px-2">
    <div class="col-12 position-fixed" style="    bottom: 56px;
">
        <form action="{{route('search-hospitals')}}" method="POST">
            @csrf
            <button type="submit" class=" btn btn-primary mb-3">Search Nearest Hospital</button>
            <input id="lat" type="hidden" name="latitude">
            <input id="long" type="hidden" name="longtitude" >
        </form>
    </div>
    <form id="search-form">
        @csrf
        <div class="col-12 position-fixed bottom-0">
            <div class="input-group mb-3 w-75">
                <input 
                type="text" 
                class="form-control" 
                name="search" 
                placeholder="Enter Text" 
                aria-label="Recipient's username" 
                aria-describedby="button-addon2"
                autocomplete="off" 
                autocorrect="off" 
                autocapitalize="off" 
                spellcheck="false">
              
                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
            </div>
        </div>
    </form>
</div>

@include('backend_app.layouts.footer')
      <!-- / Footer -->

<div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
  </div>
<script>
   if ("geolocation" in navigator) {
  navigator.geolocation.getCurrentPosition(function(position) {
    console.log("Latitude: " + position.coords.latitude + "\nLongitude: " + position.coords.longitude);
    document.getElementById('lat').value = position.coords.latitude;
    document.getElementById('long').value = position.coords.longitude;
  });
} else {
  console.log("Geolocation is not supported by this browser.");
}



</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let conversationHistory = ''; // Initialize conversation history
        let initialGreeting = true; // Variable to track if greeting has been given

        // Function to scroll to the bottom of the chat container
        function scrollToBottom() {
            const chatContainer = $('#chat-container');
            chatContainer.scrollTop(chatContainer[0].scrollHeight);
        }

        $('#search-form').on('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Get the user input from the form
            var userInput = $('input[name="search"]').val();

            // Append user message to chat
            $('#chat-container').append(`
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="float-start bg-light p-2 pe-4 rounded-3 user">
                            <span class="px-3">${userInput}</span>
                        </div>
                    </div>
                </div>
            `);

            // Update conversation history
            conversationHistory += `User: ${userInput}\n`; // Append user input to history

            // Scroll to the bottom after appending new message
            scrollToBottom();

            // Create loading dots element
            const loadingDots = $(`
                <div class="row mt-3 justify-content-end loading">
                    <div class="col-6">
                        <div class="float-end d-flex flex-row gap-3">
                            <div class="bg-light p-2 ps-4 rounded-3 ai">
                                <span class="me-3 loading-dots">...</span>
                            </div>
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSYLklAvVDg8Ri2ZY6qxje0VJOeELO0g8I4Rg&s" class="rounded-circle" style="height: 50px;" alt="">
                        </div>
                    </div>
                </div>
            `).css('animation', 'blink 1s step-end infinite');

            // Append loading dots to chat
            $('#chat-container').append(loadingDots);

            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url: '{{ route('chatbot-response') }}', // Replace with your route name
                data: {
                    search: userInput,
                    conversation: conversationHistory // Send the conversation history
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the header
                },
                success: function(response) {
                    // Remove loading dots
                    $('.loading').remove();

                    // Check if the response has choices and extract the content
                    if (response && response.choices && response.choices.length > 0) {
                        const chatbotMessage = response.choices[0].message.content; // Get the assistant's response

                        // Check if it's the initial greeting
                        if (initialGreeting && chatbotMessage.includes("It's nice to finally chat with you")) {
                            initialGreeting = false; // Update state to prevent duplicate greetings
                        }

                        // Append chatbot message to chat
                        $('#chat-container').append(`
                            <div class="row mt-3 justify-content-end">
                                <div class="col-6">
                                    <div class="float-end d-flex flex-row gap-3">
                                        <div class="bg-light p-2 ps-4 rounded-3 ai">
                                            <span class="me-3">${chatbotMessage}</span>
                                        </div>
                                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSYLklAvVDg8Ri2ZY6qxje0VJOeELO0g8I4Rg&s" class="rounded-circle" style="height: 50px;" alt="">
                                    </div>
                                </div>
                            </div>
                        `);

                        // Update conversation history with the AI's response
                        conversationHistory += `AI: ${chatbotMessage}\n`; // Append AI response to history

                        // Scroll to the bottom after appending new message
                        scrollToBottom();
                    } else {
                        $('#chat-container').append('<div class="row mt-3 justify-content-end"><div class="col-6"><div class="bg-light p-2 ps-4 rounded-3 ai"><span class="me-3">No results found.</span></div></div></div>');
                    }

                    // Scroll to the bottom after appending new message
                    scrollToBottom();
                },
                error: function(xhr, status, error) {
                    // Remove loading dots
                    $('.loading').remove();
                    console.error(xhr.responseText);
                    $('#chat-container').append('<div class="row mt-3 justify-content-end"><div class="col-6"><div class="bg-light p-2 ps-4 rounded-3 ai"><span class="me-3">An error occurred. Please try again.</span></div></div></div>');

                    // Scroll to the bottom after appending new message
                    scrollToBottom();
                }
            });

            // Clear the input field after sending
            $('input[name="search"]').val('');
        });
    });
</script>





@endsection
