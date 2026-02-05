<x-app-layout>
    <x-slot name="header">Chats</x-slot>

    <div class="flex h-[calc(100vh-120px)]">
        <!-- Chats List -->
        <div class="w-64 bg-white border-r border-gray-200 overflow-y-auto">
            <div class="p-4 space-y-2">
                @forelse($documents as $doc)
                    <a href="{{ route('chats.show', $doc->id) }}"
                       class="block p-4 rounded-lg transition-colors {{ $selectedDocument?->id === $doc->id ? 'bg-blue-50 border-l-4 border-blue-500' : 'hover:bg-gray-50' }}">
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $doc->original_filename }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ ucwords(str_replace('_', ' ', $doc->doc_type)) }}</p>
                        @if($doc->messages()->count() > 0)
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-600">{{ $doc->messages()->count() }} messages</span>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                    {{ $doc->status === 'approved' ? '✓' : ($doc->status === 'rejected' ? '✗' : '⊙') }}
                                </span>
                            </div>
                        @else
                            <p class="text-xs text-gray-400 mt-2">No messages yet</p>
                        @endif
                    </a>
                @empty
                    <p class="text-center text-gray-500 py-8">No documents yet. Upload one to start chatting!</p>
                @endforelse
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-gray-50">
            @if($selectedDocument)
                <!-- Document Header -->
                <div class="bg-white border-b border-gray-200 p-6 shadow-sm">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $selectedDocument->original_filename }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ ucwords(str_replace('_', ' ', $selectedDocument->doc_type)) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 rounded-full {{ 
                                $selectedDocument->status === 'approved' ? 'bg-green-100 text-green-800' :
                                ($selectedDocument->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                ($selectedDocument->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-gray-100 text-gray-800'))
                            }} font-semibold text-sm">
                                {{ ucfirst($selectedDocument->status) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-2">v{{ $selectedDocument->version }}</p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div id="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4">
                    <!-- Messages will be loaded here -->
                </div>

                <!-- Message Input -->
                <div class="bg-white border-t border-gray-200 p-6">
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
            @else
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">Select a document to start chatting</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($selectedDocument)
    <script>
        const documentId = {{ $selectedDocument->id }};
        const currentUserId = {{ Auth::id() }};
        const currentUserName = '{{ Auth::user()->name }}';

        async function loadMessages() {
            try {
                const response = await fetch(`/documents/${documentId}/messages`);
                const data = await response.json();
                const container = document.getElementById('messagesContainer');
                container.innerHTML = '';

                if (data.messages.length === 0) {
                    container.innerHTML = '<p class="text-center text-gray-500 py-8">No messages yet. Start the conversation!</p>';
                    return;
                }

                data.messages.forEach(msg => {
                    const isOwn = msg.user_id === currentUserId;
                    const messageEl = document.createElement('div');
                    messageEl.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} group`;
                    const cleaned = cleanCorrectionMessage(msg.message);
                    messageEl.innerHTML = `
                        <div class="relative">
                            <div class="${isOwn ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900'} px-4 py-3 rounded-lg max-w-md">
                                <p class="text-xs font-semibold mb-1">${msg.user.name}</p>
                                <div class="text-sm">${formatMessage(cleaned)}</div>
                                <p class="text-xs opacity-70 mt-1">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                            </div>
                            <div class="absolute -right-24 top-0 gap-2 flex opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button class="copy-btn p-2 rounded hover:bg-gray-300" title="Copy" data-text="${msg.message.replace(/"/g, '&quot;')}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `;
                    container.appendChild(messageEl);
                    
                    // Add copy functionality
                    const copyBtn = messageEl.querySelector('.copy-btn');
                    if (copyBtn) {
                        copyBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            const text = copyBtn.getAttribute('data-text');
                            navigator.clipboard.writeText(text).then(() => {
                                const originalTitle = copyBtn.title;
                                copyBtn.title = 'Copied!';
                                setTimeout(() => {
                                    copyBtn.title = originalTitle;
                                }, 2000);
                            });
                        });
                    }
                });

                // Scroll to bottom
                container.scrollTop = container.scrollHeight;
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatMessage(text) {
            text = escapeHtml(text);
            text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/^### (.+)$/gm, '<h3 style="font-weight: bold; margin: 0.5em 0;">$1</h3>');
            text = text.replace(/^## (.+)$/gm, '<h2 style="font-weight: bold; margin: 0.5em 0;">$1</h2>');
            text = text.replace(/^# (.+)$/gm, '<h1 style="font-weight: bold; margin: 0.5em 0;">$1</h1>');
            text = text.replace(/^- (.+)$/gm, '<li style="margin-left: 1.5em;">$1</li>');
            text = text.replace(/\n/g, '<br>');
            return text;
        }

        function cleanCorrectionMessage(text) {
            if (!text || !text.includes('Correction Request')) return text;
            const lines = text.split('\n');
            const filtered = lines.filter(line => {
                const trimmed = line.trim();
                if (!trimmed) return false;
                if (trimmed === '[]' || trimmed === '- []' || trimmed === '- Unspecified issue') return false;
                if (trimmed.startsWith('{') || trimmed.startsWith('[')) return false;
                if (trimmed.includes('"') && (trimmed.includes('{') || trimmed.includes('[') || trimmed.includes('}:') || trimmed.includes('":'))) return false;
                return true;
            });

            // Remove section headers that have no following bullet/content
            const cleaned = [];
            for (let i = 0; i < filtered.length; i++) {
                const current = filtered[i];
                const next = filtered[i + 1] || '';
                const isHeader = current.endsWith(':');
                const nextIsContent = next && !next.endsWith(':');
                if (isHeader && !nextIsContent) {
                    continue;
                }
                cleaned.push(current);
            }

            return cleaned.join('\n');
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
    @endif
</x-app-layout>
