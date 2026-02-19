<x-app-layout>
    <x-slot name="header">User Document Conversations</x-slot>

    <div class="flex h-[calc(100vh-120px)]">
        @if($users->isEmpty())
            <div class="flex-1 flex items-center justify-center bg-gray-50">
                <p class="text-gray-500">No clients with analyzed documents found.</p>
            </div>
        @else
            <div class="w-72 bg-white border-r border-gray-200 overflow-y-auto">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="font-semibold text-gray-900">Clients</h2>
                    <p class="text-xs text-gray-500 mt-1">Chat across all analyzed documents</p>
                </div>
                <div class="p-3 space-y-2">
                    @foreach($users as $user)
                        <button
                            type="button"
                            onclick="loadUserConversation({{ $user->id }})"
                            class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition border border-transparent user-item"
                            data-user-id="{{ $user->id }}"
                        >
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                            <p class="text-xs text-indigo-600 mt-1">{{ $user->documents_count }} doc{{ $user->documents_count !== 1 ? 's' : '' }}</p>
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="chatArea" class="flex-1 flex flex-col bg-gray-50">
                <div id="emptyState" class="flex-1 flex items-center justify-center">
                    <p class="text-gray-500">Select a client to start AI conversation across all their documents.</p>
                </div>

                <div id="conversationPanel" class="hidden flex-1 flex flex-col">
                    <div class="bg-white border-b border-gray-200 p-4">
                        <h2 id="clientName" class="text-lg font-bold text-gray-900"></h2>
                        <p id="clientEmail" class="text-xs text-gray-500"></p>
                    </div>

                    <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4"></div>

                    <div class="bg-white border-t border-gray-200 p-4">
                        <form id="messageForm" class="flex gap-2">
                            <input type="hidden" id="conversationId" value="">
                            <input
                                type="text"
                                id="messageInput"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Ask about this user's full document set..."
                                required
                            >
                            <button
                                type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                            >
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        let activeConversationId = null;

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        function formatMessage(text) {
            return escapeHtml(text).replace(/\n/g, '<br>');
        }

        function renderMessages(messages) {
            const container = document.getElementById('messagesContainer');
            if (!messages.length) {
                container.innerHTML = '<p class="text-center text-gray-400 py-8">Start the conversation with AI.</p>';
                return;
            }

            container.innerHTML = messages.map(msg => `
                <div class="flex ${msg.sender_type === 'ai' ? 'justify-start' : 'justify-end'}">
                    <div class="max-w-3xl ${msg.sender_type === 'ai' ? 'bg-gray-200 text-gray-900' : 'bg-indigo-600 text-white'} px-4 py-3 rounded-lg">
                        <p class="text-xs font-semibold mb-1">${msg.sender_type === 'ai' ? 'AI Assistant' : 'You'}</p>
                        <p class="text-sm">${formatMessage(msg.message)}</p>
                        <p class="text-xs opacity-70 mt-1">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                </div>
            `).join('');

            container.scrollTop = container.scrollHeight;
        }

        async function loadUserConversation(userId) {
            document.querySelectorAll('.user-item').forEach(item => item.classList.remove('bg-indigo-50', 'border-indigo-300'));
            document.querySelector(`[data-user-id="${userId}"]`)?.classList.add('bg-indigo-50', 'border-indigo-300');

            const res = await fetch(`/admin/users/${userId}/document-chat/conversation`);
            const data = await res.json();

            if (!res.ok || !data.success) {
                alert(data.error || 'Failed to load conversation');
                return;
            }

            activeConversationId = data.conversation.id;
            document.getElementById('conversationId').value = data.conversation.id;
            document.getElementById('clientName').textContent = data.user.name;
            document.getElementById('clientEmail').textContent = data.user.email;
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('conversationPanel').classList.remove('hidden');

            renderMessages(data.conversation.messages);
        }

        document.getElementById('messageForm')?.addEventListener('submit', async (event) => {
            event.preventDefault();

            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message || !activeConversationId) {
                return;
            }

            const container = document.getElementById('messagesContainer');
            if (container.innerHTML.includes('Start the conversation')) {
                container.innerHTML = '';
            }

            container.innerHTML += `
                <div class="flex justify-end">
                    <div class="max-w-3xl bg-indigo-600 text-white px-4 py-3 rounded-lg">
                        <p class="text-xs font-semibold mb-1">You</p>
                        <p class="text-sm">${escapeHtml(message)}</p>
                    </div>
                </div>
                <div class="flex justify-start" id="aiThinking">
                    <div class="max-w-3xl bg-gray-200 text-gray-900 px-4 py-3 rounded-lg">
                        <p class="text-xs font-semibold mb-1">AI Assistant</p>
                        <p class="text-sm">Thinking...</p>
                    </div>
                </div>
            `;

            container.scrollTop = container.scrollHeight;
            input.value = '';
            input.disabled = true;

            const response = await fetch(`/admin/users/document-chat/conversations/${activeConversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ message }),
            });

            const payload = await response.json();
            document.getElementById('aiThinking')?.remove();

            if (!response.ok || !payload.success) {
                alert(payload.error || 'Failed to send message');
            } else {
                container.innerHTML += `
                    <div class="flex justify-start">
                        <div class="max-w-3xl bg-gray-200 text-gray-900 px-4 py-3 rounded-lg">
                            <p class="text-xs font-semibold mb-1">AI Assistant</p>
                            <p class="text-sm">${formatMessage(payload.message.message)}</p>
                        </div>
                    </div>
                `;
                container.scrollTop = container.scrollHeight;
            }

            input.disabled = false;
            input.focus();
        });
    </script>
</x-app-layout>
