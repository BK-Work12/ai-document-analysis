<x-app-layout>
    <x-slot name="header">Analyst Saferwealth - Document Analysis</x-slot>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
#messagesContainer strong {
    font-weight: 600;
    color: inherit;
}
#messagesContainer h1, 
#messagesContainer h2, 
#messagesContainer h3 {
    font-weight: 700;
}
#messagesContainer ul,
#messagesContainer ol {
    margin: 0.5rem 0;
}
#messagesContainer li {
    margin: 0.25rem 0;
}
#messagesContainer code {
    font-family: 'Courier New', monospace;
}
</style>

<div class="flex h-[calc(100vh-120px)]">
    @if($users->isEmpty())
        <div class="flex-1 flex items-center justify-center bg-gray-50">
            <div class="text-center">
                <p class="text-gray-500">No users with documents found.</p>
            </div>
        </div>
    @else
        <!-- Users List Sidebar -->
        <div class="w-64 bg-white border-r border-gray-200 overflow-y-auto">
            <div class="p-4 space-y-2">
                @foreach($users as $user)
                    <a href="#" 
                       onclick="selectUser({{ $user->id }}); return false;"
                       class="block p-4 rounded-lg transition-colors hover:bg-gray-50 user-item"
                       data-user-id="{{ $user->id }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $user->name }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $user->documents_count }} doc{{ $user->documents_count != 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Documents List -->
        <div id="documentsPanel" class="hidden w-72 bg-white border-r border-gray-200 overflow-y-auto flex flex-col">
            <div class="p-4 border-b bg-gray-50 flex items-center gap-2">
                <button onclick="backToUsers()" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <div class="flex-1 min-w-0">
                    <h3 id="selectedUserName" class="font-semibold text-sm truncate"></h3>
                </div>
            </div>
            <div id="documentsList" class="flex-1 p-4 space-y-2 overflow-y-auto">
                <p class="text-center text-gray-400 py-8">Loading documents...</p>
            </div>
        </div>

        <!-- Chat Area -->
        <div id="chatArea" class="hidden flex-1 flex flex-col bg-gray-50">
            <!-- Document Header -->
            <div class="bg-white border-b border-gray-200 p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 id="docTitle" class="text-2xl font-bold text-gray-900"></h2>
                        <p id="docType" class="text-sm text-gray-600 mt-1"></p>
                    </div>
                    <div class="text-right">
                        <span class="px-4 py-2 rounded-full bg-purple-100 text-purple-800 font-semibold text-sm">AI Chat</span>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div id="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4">
                <p class="text-center text-gray-400 py-8">Start the conversation with AI...</p>
            </div>

            <!-- Message Input -->
            <div class="bg-white border-t border-gray-200 p-6">
                <form id="aiMessageForm" class="flex space-x-2">
                    <input type="hidden" id="conversationId" value="">
                    <input 
                        type="text" 
                        id="messageInput" 
                        name="message" 
                        placeholder="Ask about this document..." 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required
                    />
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:shadow-lg transition duration-300"
                    >
                        Send
                    </button>
                </form>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="flex-1 flex items-center justify-center bg-gray-50">
            <div class="text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <p class="text-gray-500 text-lg">Select a document to start chatting with AI</p>
            </div>
        </div>
    @endif
</div>

<script>
let currentUserId = null;
let currentDocId = null;
let currentConvId = null;

function backToUsers() {
    document.getElementById('emptyState').classList.remove('hidden');
    document.getElementById('chatArea').classList.add('hidden');
    document.getElementById('documentsPanel').classList.add('hidden');
    document.querySelectorAll('.user-item').forEach(el => el.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500'));
    currentUserId = null;
    currentDocId = null;
}

function selectUser(userId) {
    currentUserId = userId;
    
    // Highlight selected user
    document.querySelectorAll('.user-item').forEach(el => el.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500'));
    document.querySelector(`[data-user-id="${userId}"]`).classList.add('bg-blue-50', 'border-l-4', 'border-blue-500');
    
    // Show documents panel
    document.getElementById('documentsPanel').classList.remove('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('chatArea').classList.add('hidden');
    
    // Load user documents
    loadUserDocuments(userId);
}

function loadUserDocuments(userId) {
    fetch(`/admin/documents/chat/users/${userId}/documents`)
        .then(r => {
            if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
            return r.json();
        })
        .then(data => {
            if (!data.success) throw new Error(data.error || 'Failed to load documents');
            
            document.getElementById('selectedUserName').textContent = data.user.name;
            
            const docsList = document.getElementById('documentsList');
            if (data.documents.length === 0) {
                docsList.innerHTML = '<p class="text-center text-gray-400 py-8">No analyzed documents</p>';
            } else {
                docsList.innerHTML = data.documents.map(doc => `
                    <a href="#" 
                       onclick="loadDocument(${doc.id}); return false;"
                       class="block p-4 rounded-lg transition-colors hover:bg-gray-50 document-item"
                       data-doc-id="${doc.id}">
                        <h3 class="font-semibold text-gray-900 text-sm">${escapeHtml(doc.filename)}</h3>
                        <p class="text-xs text-gray-500 mt-1">${escapeHtml(doc.type)}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-600">AI Chat</span>
                            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">✓</span>
                        </div>
                    </a>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error loading documents: ' + err.message);
        });
}

function loadDocument(docId) {
    currentDocId = docId;
    
    // Highlight selected document
    document.querySelectorAll('.document-item').forEach(el => el.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500'));
    document.querySelector(`[data-doc-id="${docId}"]`).classList.add('bg-blue-50', 'border-l-4', 'border-blue-500');
    
    fetch(`/admin/documents/${docId}/conversations`)
        .then(r => {
            if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
            return r.json();
        })
        .then(data => {
            if (!data.success) throw new Error(data.error || 'Failed to load conversation');
            
            currentConvId = data.conversation.id;
            
            // Update document info
            document.getElementById('docTitle').textContent = data.document.filename;
            document.getElementById('docType').textContent = data.document.type + ' • User: ' + data.document.user;
            document.getElementById('conversationId').value = data.conversation.id;
            
            // Render messages
            const container = document.getElementById('messagesContainer');
            if (data.conversation.messages.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-400 py-8">Start the conversation with AI...</p>';
            } else {
                container.innerHTML = data.conversation.messages.map(msg => `
                    <div class="flex ${msg.sender_type === 'ai' ? 'justify-start' : 'justify-end'} mb-4">
                        <div class="max-w-2xl">
                            <div class="${msg.sender_type === 'ai' ? 'bg-gray-200 text-gray-900' : 'bg-purple-600 text-white'} px-4 py-3 rounded-lg">
                                <p class="text-xs font-semibold mb-1">${msg.sender_type === 'ai' ? 'AI Assistant' : 'You'}</p>
                                <p class="text-sm message-content" data-msg-id="${msg.id}" data-msg-text="${escapeHtml(msg.message)}">${formatMessage(msg.message)}</p>
                                <p class="text-xs opacity-70 mt-1">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                            </div>
                            ${msg.sender_type === 'ai' ? `
                                <div class="flex gap-2 mt-2">
                                    <button class="copy-btn text-gray-500 hover:text-gray-700 transition" data-msg-id="${msg.id}" title="Copy">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <button class="send-btn text-gray-500 hover:text-gray-700 transition" data-msg-id="${msg.id}" title="Send to Client">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                    </button>
                                    <button class="edit-btn text-gray-500 hover:text-gray-700 transition" data-msg-id="${msg.id}" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `).join('');
                
                // Attach event listeners
                setupMessageButtonListeners();
            }
            
            container.scrollTop = container.scrollHeight;
            
            // Show chat area
            document.getElementById('chatArea').classList.remove('hidden');
            document.getElementById('messageInput').focus();
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error loading document: ' + err.message);
        });
}

document.getElementById('aiMessageForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    const convId = document.getElementById('conversationId').value;
    
    if (!message || !convId) return;
    
    const container = document.getElementById('messagesContainer');
    if (container.innerHTML.includes('Start the conversation')) {
        container.innerHTML = '';
    }
    
    // Add user message
    container.innerHTML += `
        <div class="flex justify-end">
            <div class="bg-purple-600 text-white px-4 py-3 rounded-lg max-w-2xl">
                <p class="text-xs font-semibold mb-1">You</p>
                <p class="text-sm">${escapeHtml(message)}</p>
                <p class="text-xs opacity-70 mt-1">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
            </div>
        </div>
    `;
    
    // Show AI thinking
    container.innerHTML += `
        <div class="flex justify-start" id="aiThinking">
            <div class="bg-gray-200 text-gray-900 px-4 py-3 rounded-lg">
                <p class="text-xs font-semibold mb-1">AI Assistant</p>
                <p class="text-sm">Thinking...</p>
            </div>
        </div>
    `;
    
    container.scrollTop = container.scrollHeight;
    messageInput.value = '';
    messageInput.disabled = true;
    
    try {
        const response = await fetch(`/admin/conversations/${convId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message }),
        });

        if (response.ok) {
            const data = await response.json();
            document.getElementById('aiThinking')?.remove();
            
            container.innerHTML += `
                <div class="flex justify-start">
                    <div class="bg-gray-200 text-gray-900 px-4 py-3 rounded-lg max-w-2xl">
                        <p class="text-xs font-semibold mb-1">AI Assistant</p>
                        <p class="text-sm">${formatMessage(data.message.message)}</p>
                        <p class="text-xs opacity-70 mt-1">${new Date(data.message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                </div>
            `;
            
            container.scrollTop = container.scrollHeight;
        } else {
            alert('Error sending message');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error sending message');
    } finally {
        messageInput.disabled = false;
        messageInput.focus();
    }
});

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
    text = text.replace(/\n/g, '<br>');
    return text;
}

function copyMessage(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Message copied to clipboard');
    }).catch(() => {
        alert('Failed to copy message');
    });
}

function sendToClient(msgId, text) {
    if (!confirm('Send this message to client?')) return;
    
    const convId = document.getElementById('conversationId').value;
    
    fetch(`/admin/conversations/${convId}/messages/${msgId}/send-to-client`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Message sent to client successfully');
        } else {
            alert('Error: ' + (data.error || 'Failed to send message'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Failed to send message to client');
    });
}

function editMessage(msgId, text) {
    const newText = prompt('Edit message:', text);
    if (newText === null || newText === text) return;
    
    const convId = document.getElementById('conversationId').value;
    
    fetch(`/admin/conversations/${convId}/messages/${msgId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ message: newText }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Message updated successfully');
            // Reload the conversation
            const docId = document.querySelector('[data-doc-id].bg-blue-50')?.getAttribute('data-doc-id');
            if (docId) loadDocument(parseInt(docId));
        } else {
            alert('Error: ' + (data.error || 'Failed to update message'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Failed to update message');
    });
}

function setupMessageButtonListeners() {
    // Copy button listeners
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const msgId = this.dataset.msgId;
            const msgElement = document.querySelector(`.message-content[data-msg-id="${msgId}"]`);
            if (msgElement) {
                const text = msgElement.dataset.msgText;
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                }).catch(() => {
                    alert('Failed to copy message');
                });
            }
        });
    });
    
    // Send to client button listeners
    document.querySelectorAll('.send-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const msgId = this.dataset.msgId;
            const msgElement = document.querySelector(`.message-content[data-msg-id="${msgId}"]`);
            if (msgElement) {
                const text = msgElement.dataset.msgText;
                sendToClient(msgId, text);
            }
        });
    });
    
    // Edit button listeners
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const msgId = this.dataset.msgId;
            const msgElement = document.querySelector(`.message-content[data-msg-id="${msgId}"]`);
            if (msgElement) {
                const text = msgElement.dataset.msgText;
                editMessage(msgId, text);
            }
        });
    });
}
</script>
</x-app-layout>
