<x-app-layout>
    <x-slot name="header">{{ $user->name }}'s Documents & Messages</x-slot>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">{{ $user->name }}'s Documents</h1>
            <p class="text-gray-600 mt-1">{{ $user->email }} • {{ ucfirst($user->role) }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            ← Back to Users
        </a>
    </div>

    @if($documents->isEmpty())
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <p class="text-gray-500">This user has no documents yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Documents List -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="font-semibold">Documents ({{ $documents->count() }})</h2>
                </div>
                <div class="max-h-[calc(100vh-300px)] overflow-y-auto">
                    @foreach($documents as $doc)
                        <a href="#" 
                           onclick="loadDocumentMessages({{ $doc->id }}); return false;"
                           class="block p-4 hover:bg-blue-50 border-b cursor-pointer transition document-item"
                           data-doc-id="{{ $doc->id }}">
                            <div class="text-sm font-medium truncate">{{ $doc->original_filename }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $doc->doc_type }}</div>
                            <div class="text-xs mt-2 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z"/></svg>
                                    {{ $doc->messages_count }} message{{ $doc->messages_count != 1 ? 's' : '' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Chat Panel -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow flex flex-col" style="height: calc(100vh - 250px);">
                <div id="emptyState" class="flex-1 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <p>Select a document to view messages</p>
                    </div>
                </div>

                <div id="chatPanel" class="hidden flex-1 flex flex-col">
                    <!-- Header -->
                    <div class="border-b p-4 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h3 id="docName" class="font-semibold text-lg"></h3>
                        <p id="docInfo" class="text-xs text-gray-500 mt-1"></p>
                    </div>

                    <!-- Messages -->
                    <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-50 to-white">
                        <!-- Messages loaded dynamically -->
                    </div>

                    <!-- Input -->
                    <div class="border-t p-4 bg-white shadow-inner">
                        <form onsubmit="sendMessage(event)" class="flex gap-3">
                            <input type="hidden" id="documentId" value="">
                            <input type="text" 
                                   id="messageInput" 
                                   placeholder="Type your reply..." 
                                   class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required
                                   autocomplete="off">
                            <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 font-medium transition-all shadow-md hover:shadow-lg">
                                Send Reply
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
let currentDocId = null;

function loadDocumentMessages(docId) {
    currentDocId = docId;
    
    // Highlight selected document
    document.querySelectorAll('.document-item').forEach(el => el.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-600'));
    document.querySelector(`[data-doc-id="${docId}"]`).classList.add('bg-blue-50', 'border-l-4', 'border-blue-600');
    
    fetch(`/admin/users/documents/${docId}/messages`)
        .then(r => {
            if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
            return r.json();
        })
        .then(data => {
            if (!data.success) throw new Error(data.error || 'Failed to load messages');
            
            document.getElementById('docName').textContent = data.document.filename;
            document.getElementById('docInfo').textContent = `${data.document.type} • User: ${data.document.user}`;
            document.getElementById('documentId').value = docId;
            
            // Render messages
            const container = document.getElementById('messagesContainer');
            if (data.messages.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-400 py-8">No messages yet. Start the conversation!</div>';
            } else {
                container.innerHTML = data.messages.map(msg => `
                    <div class="flex ${msg.is_admin ? 'justify-end' : 'justify-start'} mb-4 animate-fade-in">
                        <div class="max-w-2xl">
                            <div class="text-xs text-gray-500 mb-1 ${msg.is_admin ? 'text-right mr-1' : 'ml-1'}">
                                ${msg.user_name} ${msg.is_admin ? '(Admin)' : '(Client)'}
                            </div>
                            <div class="px-4 py-3 rounded-lg ${msg.is_admin ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-900'} shadow-sm">
                                ${escapeHtml(msg.message)}
                            </div>
                            <div class="text-xs text-gray-400 mt-1 ${msg.is_admin ? 'text-right mr-1' : 'ml-1'}">
                                ${new Date(msg.created_at).toLocaleString()}
                            </div>
                        </div>
                    </div>
                `).join('');
            }
            
            container.scrollTop = container.scrollHeight;
            
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('chatPanel').classList.remove('hidden');
            document.getElementById('messageInput').focus();
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error loading messages: ' + err.message);
        });
}

function sendMessage(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    const docId = document.getElementById('documentId').value;
    
    if (!message) return;
    
    const sendBtn = e.target.querySelector('button[type="submit"]');
    const originalText = sendBtn.textContent;
    sendBtn.textContent = 'Sending...';
    sendBtn.disabled = true;
    messageInput.disabled = true;
    
    fetch(`/admin/users/documents/${docId}/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ message })
    })
    .then(r => {
        if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
        return r.json();
    })
    .then(data => {
        if (!data.success) throw new Error(data.error || 'Failed to send message');
        
        messageInput.value = '';
        
        // Add message to UI
        const container = document.getElementById('messagesContainer');
        if (container.innerHTML.includes('No messages yet')) {
            container.innerHTML = '';
        }
        
        container.innerHTML += `
            <div class="flex justify-end mb-4 animate-fade-in">
                <div class="max-w-2xl">
                    <div class="text-xs text-gray-500 mb-1 text-right mr-1">
                        ${data.message.user_name} (Admin)
                    </div>
                    <div class="px-4 py-3 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-sm">
                        ${escapeHtml(data.message.message)}
                    </div>
                    <div class="text-xs text-gray-400 mt-1 text-right mr-1">
                        ${new Date(data.message.created_at).toLocaleString()}
                    </div>
                </div>
            </div>
        `;
        
        container.scrollTop = container.scrollHeight;
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error sending message: ' + err.message);
    })
    .finally(() => {
        sendBtn.textContent = originalText;
        sendBtn.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
</x-app-layout>
