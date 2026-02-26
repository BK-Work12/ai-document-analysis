<x-app-layout>
    <x-slot name="header">{{ $document->doc_type }} - Chat</x-slot>

    <div class="flex flex-col h-screen p-8 bg-gray-50">
        <!-- Document Info -->
        <div class="mb-6 p-4 bg-white rounded-lg shadow border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $document->doc_type }}</h2>
                    <p class="text-sm text-gray-600">{{ $document->original_filename }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold">
                        <span class="px-3 py-1 rounded-full {{ 
                            $document->status === 'approved' ? 'bg-green-100 text-green-800' :
                            $document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            $document->status === 'rejected' ? 'bg-red-100 text-red-800' :
                            'bg-gray-100 text-gray-800'
                        }}">
                            {{ ucfirst($document->status) }}
                        </span>
                    </p>
                    <p class="text-xs text-gray-500 mt-2">v{{ $document->version }}</p>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto mb-6 space-y-4 bg-white rounded-lg shadow p-6">
            <!-- Messages will be loaded here -->
        </div>

        <!-- Message Input -->
        <form id="messageForm" class="flex space-x-2">
            @csrf
            <input 
                type="text" 
                id="messageInput" 
                name="message" 
                placeholder="Type your message..." 
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            />
            <button 
                type="submit" 
                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg hover:shadow-lg transition duration-300"
            >
                Send
            </button>
        </form>
    </div>

    <script>
        const documentId = {{ $document->id }};
        const currentUserId = {{ Auth::id() }};
        const currentUserName = '{{ Auth::user()->name }}';

        async function loadMessages() {
            try {
                const response = await fetch(`/documents/${documentId}/messages`);
                const data = await response.json();
                const container = document.getElementById('messagesContainer');
                const previousScrollTop = container.scrollTop;
                container.innerHTML = '';

                if (data.messages.length === 0) {
                    container.innerHTML = '<p class="text-center text-gray-500 py-8">No messages yet. Start the conversation!</p>';
                    return;
                }

                data.messages.forEach(msg => {
                    const isOwn = msg.user_id === currentUserId;
                    const messageEl = document.createElement('div');
                    messageEl.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;
                    messageEl.innerHTML = `
                        <div class="${isOwn ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900'} px-4 py-2 rounded-lg max-w-xs">
                            <p class="text-xs font-semibold mb-1">${msg.user.name}</p>
                            <p>${escapeHtml(msg.message)}</p>
                            <p class="text-xs opacity-70 mt-1">${new Date(msg.created_at).toLocaleTimeString()}</p>
                        </div>
                    `;
                    container.appendChild(messageEl);
                });

                container.scrollTop = Math.min(previousScrollTop, container.scrollHeight);
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.getElementById('messageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();

            if (!message) return;

            try {
                const response = await fetch(`/documents/${documentId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({ message }),
                });

                if (response.ok) {
                    messageInput.value = '';
                    await loadMessages();
                } else {
                    alert('Error sending message');
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });

        // Load messages on page load
        loadMessages();

        // Refresh messages every 2 seconds
        setInterval(loadMessages, 2000);
    </script>
</x-app-layout>
