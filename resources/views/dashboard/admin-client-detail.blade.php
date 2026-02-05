<x-app-layout>
    <x-slot name="header">{{ $user->name }} - Client Details</x-slot>

    <div class="p-8 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 grid grid-cols-3 gap-6">
            <!-- Client Info Card -->
            <div class="col-span-2 bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-600 mt-1">Client ID: {{ $user->id }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $isComplete ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $isComplete ? '✓ Complete' : '⚠ Incomplete' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-6 border-t pt-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Address</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Verified</p>
                        <div class="mt-1">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-medium">
                                    ✓ Yes
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm font-medium">
                                    ✗ No
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Member Since</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined Time</p>
                        <p class="text-lg font-medium text-gray-900 mt-1">{{ $user->created_at->format('H:i') }}</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t">
                    <a href="{{ route('admin.clients.email-logs', $user) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                        View Email Logs →
                    </a>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-sm p-6 border border-blue-100">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-600">Total Documents</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $documents->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-green-600">{{ $documents->where('status', 'approved')->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Pending Review</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $documents->where('status', 'pending')->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Needs Correction</p>
                        <p class="text-2xl font-bold text-red-600">{{ $documents->where('status', 'needs_correction')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Documents</h3>
            </div>

            @if($documents->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-gray-600">No documents submitted yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @php
                        $groupedDocs = $documents->sortByDesc('created_at')->groupBy('doc_type');
                    @endphp

                    @foreach($groupedDocs as $docType => $docs)
                        @foreach($docs as $doc)
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $doc->original_filename }}</h4>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ 
                                                $doc->status === 'approved' ? 'bg-green-100 text-green-800' :
                                                ($doc->status === 'needs_correction' ? 'bg-red-100 text-red-800' :
                                                ($doc->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-gray-100 text-gray-800'))
                                            }}">
                                                {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                            <span>{{ ucwords(str_replace('_', ' ', $docType)) }}</span>
                                            <span>v{{ $doc->version }}</span>
                                            <span>{{ number_format($doc->size_bytes / 1024, 2) }} KB</span>
                                            <span>{{ $doc->uploaded_at?->format('M d, Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($doc->notes)
                                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-sm text-blue-900"><strong>Notes:</strong> {{ $doc->notes }}</p>
                                    </div>
                                @endif

                                @if($doc->correction_feedback)
                                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-sm text-red-900"><strong>Correction Feedback:</strong> {{ $doc->correction_feedback }}</p>
                                    </div>
                                @endif

                                @if($doc->status !== 'approved')
                                    <div class="flex gap-2 flex-wrap">
                                        <form action="{{ route('admin.documents.status', [$doc, 'approved']) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                                ✓ Approve
                                            </button>
                                        </form>
                                        
                                        <button type="button" onclick="openCorrectionModal({{ $doc->id }})" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                            Request Correction
                                        </button>

                                        <a href="{{ route('admin.chats.show', [$user->id, $doc->id]) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                            💬 Chat
                                        </a>

                                        <a href="{{ route('documents.preview', $doc->id) }}" target="_blank" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition inline-flex items-center gap-2">
                                            👁 Preview
                                        </a>
                                    </div>
                                @else
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.chats.show', [$user->id, $doc->id]) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                            💬 Chat
                                        </a>

                                        <a href="{{ route('documents.preview', $doc->id) }}" target="_blank" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition inline-flex items-center gap-2">
                                            👁 Preview
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Correction Modal -->
    <div id="correctionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4 text-gray-900">Request Correction</h3>
            <form id="correctionForm" method="POST">
                @csrf
                @method('PATCH')
                <textarea name="notes" class="w-full border border-gray-300 rounded-lg p-3 mb-4 focus:ring-2 focus:ring-indigo-500" placeholder="Enter feedback for the client..." rows="4" required></textarea>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                        Send Request
                    </button>
                    <button type="button" onclick="closeCorrectionModal()" class="flex-1 bg-gray-200 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-300 font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCorrectionModal(docId) {
            const form = document.getElementById('correctionForm');
            form.action = `/admin/documents/${docId}/status/needs_correction`;
            document.getElementById('correctionModal').classList.remove('hidden');
        }

        function closeCorrectionModal() {
            document.getElementById('correctionModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
