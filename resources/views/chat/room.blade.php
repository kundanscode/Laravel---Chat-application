<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secret Chat Room</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom scrollbar for webkit */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            bg: #1f2937;
        }

        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>
</head>

<body class="bg-gray-900 text-white h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 p-4 shadow-md flex justify-between items-center z-10">
        <div class="flex items-center">
            <div class="w-3 h-3 rounded-full bg-green-500 mr-2 animate-pulse"></div>
            <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-500">
                Secret Chat
            </h1>
        </div>
        <div class="text-sm text-gray-400">
            Logged in as: <span class="font-semibold text-white">{{ $userName }}</span>
            <form action="{{ route('chat.leave') }}" method="POST" class="inline ml-4">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300 transition underline">Leave</button>
            </form>
        </div>
    </header>

    <!-- Chat Area -->
    <main id="chat-container" class="flex-1 overflow-y-auto p-4 space-y-4 scroll-smooth">
        <!-- Messages will be appended here -->
        <div class="text-center text-gray-500 text-sm mt-4">
            <p>Welcome to the secret chat.</p>
            <p>Share this URL with a friend to start chatting.</p>
            <div class="mt-2 p-2 bg-gray-800 rounded border border-gray-700 inline-flex items-center space-x-2">
                <code class="text-xs text-blue-300 select-all">{{ route('chat.join', $secretKey) }}</code>
                <button onclick="navigator.clipboard.writeText('{{ route('chat.join', $secretKey) }}')"
                    class="text-xs text-gray-400 hover:text-white" title="Copy">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>
        </div>
    </main>

    <!-- Input Area -->
    <footer class="bg-gray-800 border-t border-gray-700 p-4">
        <form id="message-form" class="max-w-4xl mx-auto relative flex items-center gap-2">
            <input type="text" id="message-input" autocomplete="off"
                class="flex-1 bg-gray-700 text-white placeholder-gray-400 border border-gray-600 rounded-full px-6 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                placeholder="Type a message...">

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-500 text-white rounded-full p-3 transition transform active:scale-95 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
            </button>
        </form>
    </footer>

    <script>
        const secretKey = "{{ $secretKey }}";
        const currentUser = "{{ $userName }}";
        const chatContainer = document.getElementById('chat-container');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');

        // Scroll to bottom
        function scrollToBottom() {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Append message to UI
        function appendMessage(user, text, isMe) {
            const div = document.createElement('div');
            div.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;

            // Time formatting (simple)
            const time = new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });

            div.innerHTML = `
                <div class="max-w-[75%] rounded-2xl px-4 py-2 shadow-sm ${isMe ? 'bg-blue-600 text-white rounded-br-none' : 'bg-gray-700 text-white rounded-bl-none'}">
                    ${!isMe ? `<div class="text-xs text-blue-300 font-bold mb-1">${user}</div>` : ''}
                    <p class="text-sm leading-relaxed">${text}</p>
                    <div class="text-[10px] opacity-70 text-right mt-1">${time}</div>
                </div>
            `;
            chatContainer.appendChild(div);
            scrollToBottom();
        }

        // Send Message
        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            // Optimistic UI update
            appendMessage(currentUser, message, true);
            messageInput.value = '';

            try {
                await fetch(`{{ url('/chat') }}/${secretKey}/message`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message
                    })
                });
            } catch (error) {
                console.error('Error sending message:', error);
                // Ideally show error state here
            }
        });

        // Listen for events
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                window.Echo.channel(`chat.${secretKey}`)
                    .listen('.message.sent', (e) => {
                        console.log('Message received:', e);
                        if (e.userName !== currentUser) {
                            appendMessage(e.userName, e.message, false);
                        }
                    });
            } else {
                console.warn('Laravel Echo not loaded yet.');
            }
        });
    </script>
</body>

</html>
