<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div id="app" class="container mx-auto my-10">
        <div class="flex justify-center">
            <div class="w-1/2">
                <!-- Chat messages container -->
                <div id="chatMessages" class="border border-gray-300 p-4 h-64 overflow-y-auto"></div>
                
                <!-- Chat input form -->
                <form id="chatForm" class="flex mt-4">
                <input type="text" id="receiverIdInput" name="receiver_id" value="{{ $receiverId }}" hidden>

                    <input type="text" id="messageInput" class="flex-1 mr-2 p-2 border rounded" placeholder="Type your message...">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
    
      
    </script>
   <script src="{{ asset('js/app.js') }}"></script>
    <script>
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const receiverIdInput = document.getElementById('receiverIdInput');

        // Function to fetch messages from the server
        function fetchMessages() {
            const senderId = '{{ Auth::id() }}';
            const receiverId = receiverIdInput.value;
            axios.get(`/get-messages/${senderId}/${receiverId}`)
                .then(response => {
                    const messages = response.data;
                    messages.forEach(message => {
                        appendMessage(message.message);
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Event listener for form submission
        chatForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const message = messageInput.value.trim();
            const receiverId = receiverIdInput.value;
            if (message !== '') {
                // Send the message to the server
                axios.post('/send-message', { message, receiver_id: receiverId })
                     .then(response => {
                         console.log(response.data);
                     })
                     .catch(error => {
                         console.error(error);
                     });
                messageInput.value = '';
            }
        });

        // Function to append a new message to the chat window
        function appendMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.innerText = message;
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Fetch messages when the page loads
        fetchMessages();

        // Real-time message updates using Pusher and Echo
        const userId = '{{ Auth::id() }}';
        const echo = window.Echo.private(`myPrivateChannel.${userId}`);

        echo.listen('.App\\Events\\ChatSent', (e) => {
            console.log(e.message);
            appendMessage(e.message);
        });
    </script>
</body>
</html>
