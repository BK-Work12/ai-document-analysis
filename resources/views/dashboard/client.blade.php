<x-app-layout>
    <x-slot name="header">Your Document Checklist</x-slot>

    <div class="min-h-screen bg-slate-50">
        <!-- Progress Bar and Missing Files Alert -->
        <div class="w-full bg-white pt-6 px-0 sm:px-6">
            @php
                $totalRequired = $requirements->count();
                $uploadedTypes = $documents->pluck('doc_type')->unique();
                $requiredTypes = $requirements->pluck('doc_type');
                $missingTypes = $requiredTypes->diff($uploadedTypes);
                $uploadedCount = $uploadedTypes->count();
                $progressPercent = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;
            @endphp
            <div class="mb-6 px-0 sm:px-4 md:px-8">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                        <span class="text-base font-semibold text-teal-900">Upload Progress</span>
                    </div>
                    <span class="text-sm font-semibold text-teal-700">{{ $uploadedCount }} <span class="text-gray-400">/</span> {{ $totalRequired }}</span>
                </div>
                <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden shadow-sm mb-2">
                    <div class="absolute left-0 top-0 h-4 bg-gradient-to-r from-teal-600 to-emerald-500 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-teal-900">{{ $progressPercent }}%</div>
                </div>
                @if ($missingTypes->count() > 0)
                    <div class="mb-2 p-4 rounded-xl border-l-4 border-red-500 bg-red-50 flex items-start gap-3 shadow-sm w-full">
                        <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/></svg>
                        <div>
                            <div class="font-bold text-red-700 mb-1">Still needed</div>
                            <ul class="list-disc ml-6 text-red-700 text-sm">
                                @foreach ($requirements as $req)
                                    @if ($missingTypes->contains($req->doc_type))
                                        <li>{{ ucfirst(str_replace('_', ' ', $req->doc_type)) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                            <div class="text-xs text-red-600 mt-2 font-medium">Upload these documents to complete your checklist.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="max-w-7xl mx-auto p-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Sidebar - Upload Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-y-auto p-6 sticky top-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-700 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Upload a Document</h3>
                        </div>

                        <div id="uploadMessage" class="hidden mb-4 p-3 rounded-lg text-sm"></div>

                        <form id="uploadForm" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            <div>
                                <label for="doc_type" class="block text-xs font-semibold text-teal-800 uppercase mb-2">Document type *</label>
                                <select name="doc_type" id="doc_type" required
                                    class="w-full px-3 py-2.5 border border-teal-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-600 focus:border-teal-600 transition">
                                    <option value="">Choose a document type</option>
                                    @foreach ($requirements as $req)
                                        <option value="{{ $req->doc_type }}">
                                            {{ ucfirst(str_replace('_', ' ', $req->doc_type)) }}
                                            @if ($req->required)
                                                (Required)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="file" class="block text-xs font-semibold text-teal-800 uppercase mb-2">File *</label>
                                <div id="dropZone" class="flex justify-center px-4 py-6 border-2 border-teal-200 border-dashed rounded-lg hover:border-teal-400 transition cursor-pointer hover:bg-emerald-50">
                                    <div class="space-y-1 text-center w-full">
                                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-teal-700 hover:text-teal-600 block cursor-pointer">Click to choose a file</span>
                                        <input id="file" name="file" type="file" required
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="sr-only">
                                        <p class="text-xs text-gray-500">or drag and drop here</p>
                                        <p class="text-xs text-gray-400">PDF, DOC, DOCX, JPG, PNG (max 20MB)</p>
                                    </div>
                                </div>
                                <p id="fileName" class="text-xs text-gray-600 mt-2"></p>
                            </div>

                            <button type="submit" id="uploadBtn"
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-teal-700 to-emerald-600 text-white rounded-lg font-semibold text-sm hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                Upload now
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Main Area - Documents Table -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-700 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Uploaded Documents</h3>
                            </div>

                            <!-- Table Controls -->
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input type="text" id="tableSearch" placeholder="Search by document name..."
                                    class="flex-1 px-4 py-2.5 border border-teal-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-600 focus:border-teal-600">
                                <select id="statusFilter" class="px-4 py-2.5 border border-teal-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-600 focus:border-teal-600 min-w-[160px]">
                                    <option value="">All Status</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Under Review</option>
                                    <option value="needs_correction">Action Needed</option>
                                </select>
                            </div>
                        </div>

                        @if ($documents->isEmpty())
                            <div class="p-12">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-600 text-base mt-4 font-medium">No documents yet</p>
                                    <p class="text-gray-400 text-sm mt-2">Use the upload form to add your first document.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex-1 overflow-x-auto">
                                <table id="documentsTable" class="w-full text-sm">
                                    <thead class="sticky top-0 bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="text-left px-6 py-4 font-semibold text-gray-700 w-12">No.</th>
                                            <th class="text-left px-6 py-4 font-semibold text-gray-700 cursor-pointer hover:bg-gray-100" onclick="sortTable(1)">
                                                Document Type <span class="text-xs text-gray-400">↕</span>
                                            </th>
                                            <th class="text-left px-6 py-4 font-semibold text-gray-700 cursor-pointer hover:bg-gray-100" onclick="sortTable(2)">
                                                Status <span class="text-xs text-gray-400">↕</span>
                                            </th>
                                            <th class="text-left px-6 py-4 font-semibold text-gray-700 cursor-pointer hover:bg-gray-100" onclick="sortTable(3)">
                                                Uploaded <span class="text-xs text-gray-400">↕</span>
                                            </th>
                                            <th class="text-left px-6 py-4 font-semibold text-gray-700 w-16">Version</th>
                                            <th class="text-left px-6 py-4 font-semibold text-gray-700 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200" id="tableBody">
                                        @forelse($documents as $index => $doc)
                                            <tr class="hover:bg-gray-50 transition-colors table-row" data-status="{{ $doc->status }}" data-type="{{ $doc->doc_type }}" data-date="{{ $doc->uploaded_at?->timestamp ?? 0 }}" data-index="{{ $index + 1 }}">
                                                <td class="px-6 py-4 text-gray-600 font-medium w-12"><span class="sr-number">{{ $index + 1 }}</span></td>
                                                <td class="px-6 py-4">
                                                    <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $doc->doc_type)) }}</span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                                        @if ($doc->status === 'approved') bg-green-100 text-green-800
                                                        @elseif($doc->status === 'needs_correction') bg-red-100 text-red-800
                                                        @elseif($doc->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800 @endif
                                                    ">
                                                        @if ($doc->status === 'needs_correction')
                                                            Action Needed
                                                        @elseif($doc->status === 'pending')
                                                            Under Review
                                                        @else
                                                            {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-gray-600">{{ $doc->uploaded_at?->format('M d, Y') ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 text-gray-600 text-xs font-medium">v{{ $doc->version }}</td>
                                                <td class="px-6 py-4">
                                                    <div class="flex gap-3">
                                                        <button onclick="downloadDocument({{ $doc->id }})"
                                                            class="text-teal-700 hover:text-teal-900 text-xs font-medium hover:underline">Download</button>
                                                        <button
                                                            onclick="openChatModal({{ $doc->id }}, '{{ ucfirst(str_replace('_', ' ', $doc->doc_type)) }}')"
                                                            class="text-emerald-700 hover:text-emerald-900 text-xs font-medium hover:underline">Ask a question</button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if ($doc->correction_feedback || $doc->notes)
                                                <tr class="bg-red-50 table-row" data-status="{{ $doc->status }}" data-type="{{ $doc->doc_type }}" data-date="{{ $doc->uploaded_at?->timestamp ?? 0 }}" data-index="{{ $index + 1 }}">
                                                    <td colspan="6" class="px-6 py-4">
                                                        <div class="flex items-start gap-3">
                                                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            <div class="text-xs">
                                                                @if ($doc->correction_feedback)
                                                                    <p class="font-semibold text-red-800">What needs to be fixed:</p>
                                                                    <p class="text-red-700 mt-1">{{ $doc->correction_feedback }}</p>
                                                                    @if (!is_null($doc->confidence_score))
                                                                        <p class="text-red-600 mt-1">AI match score: {{ $doc->confidence_score }}%</p>
                                                                    @endif
                                                                @endif
                                                                @if ($doc->notes)
                                                                    <p class="font-semibold text-red-800 mt-2">Reviewer notes:</p>
                                                                    <p class="text-red-700 mt-1">{{ $doc->notes }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-gray-600 text-sm">No matching documents found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Controls -->
                            <div class="p-6 border-t border-gray-200 bg-gray-50">
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <div class="text-sm text-gray-600">
                                        Showing <span id="startRecord">1</span>-<span id="endRecord">20</span> of <span id="totalRecords">{{ count($documents) }}</span> document(s)
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="prevPage()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition">← Previous</button>
                                        <span id="pageInfo" class="px-3 py-2 text-sm text-gray-600">Page <span id="currentPage" class="font-semibold">1</span></span>
                                        <button onclick="nextPage()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition">Next →</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Table Navigation and Search
        let currentPage = 1;
        const recordsPerPage = 20;
        let allRows = [];
        let filteredRows = [];

        function initializeTable() {
            allRows = Array.from(document.querySelectorAll('#tableBody .table-row'));
            applyFilters();
        }

        function applyFilters() {
            const searchTerm = document.getElementById('tableSearch')?.value?.toLowerCase() || '';
            const statusFilter = document.getElementById('statusFilter')?.value || '';

            filteredRows = allRows.filter(row => {
                const type = row.getAttribute('data-type')?.toLowerCase() || '';
                const status = row.getAttribute('data-status') || '';
                const matchesSearch = type.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;
                return matchesSearch && matchesStatus;
            });

            currentPage = 1;
            updateTable();
        }

        function updateTable() {
            // Hide all rows
            allRows.forEach(row => row.style.display = 'none');

            // Show filtered and paginated rows
            const start = (currentPage - 1) * recordsPerPage;
            const end = start + recordsPerPage;
            
            filteredRows.slice(start, end).forEach((row, index) => {
                row.style.display = 'table-row';
                const srNumber = row.querySelector('.sr-number');
                if (srNumber) {
                    srNumber.textContent = start + index + 1;
                }
            });

            // Update pagination info
            const totalPages = Math.ceil(filteredRows.length / recordsPerPage);
            document.getElementById('startRecord').textContent = filteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('endRecord').textContent = Math.min(end, filteredRows.length);
            document.getElementById('totalRecords').textContent = filteredRows.length;
            document.getElementById('currentPage').textContent = currentPage;
        }

        function nextPage() {
            const totalPages = Math.ceil(filteredRows.length / recordsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();
            }
        }

        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                updateTable();
            }
        }

        function sortTable(columnIndex) {
            const isAscending = !allRows[0]?.dataset.sortAsc?.[columnIndex];
            
            filteredRows.sort((a, b) => {
                let aVal = a.children[columnIndex]?.textContent || '';
                let bVal = b.children[columnIndex]?.textContent || '';

                // Try numeric sort for dates and versions
                const aNum = parseInt(a.getAttribute('data-date')) || 0;
                const bNum = parseInt(b.getAttribute('data-date')) || 0;
                
                if (columnIndex === 3 && (aNum || bNum)) {
                    return isAscending ? aNum - bNum : bNum - aNum;
                }

                // String sort
                aVal = aVal.trim().toLowerCase();
                bVal = bVal.trim().toLowerCase();
                return isAscending ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            });

            currentPage = 1;
            updateTable();
        }

        // Event listeners
        document.getElementById('tableSearch')?.addEventListener('input', applyFilters);
        document.getElementById('statusFilter')?.addEventListener('change', applyFilters);

        // Initialize on load
        document.addEventListener('DOMContentLoaded', initializeTable);

        // File input and drop zone
        const fileInput = document.getElementById('file');
        const dropZone = document.getElementById('dropZone');

        // Click on drop zone to trigger file input
        dropZone?.addEventListener('click', (e) => {
            if (e.target.tagName !== 'INPUT') {
                fileInput.click();
            }
        });

        // File name display
        fileInput?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            document.getElementById('fileName').textContent = fileName ? `✓ ${fileName}` : '';
        });

        // Drag and drop support
        dropZone?.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-teal-500', 'bg-emerald-50');
        });

        dropZone?.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-teal-500', 'bg-emerald-50');
        });

        dropZone?.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-teal-500', 'bg-emerald-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        });

        // Upload form handler
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('uploadBtn');
            const originalText = btn.innerHTML;
            const messageDiv = document.getElementById('uploadMessage');

            btn.disabled = true;
            btn.innerHTML =
                '<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading...';

            const formData = new FormData(e.target);

            try {
                const response = await fetch('{{ route('documents.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    messageDiv.className =
                        'p-3 rounded-lg bg-green-100 border border-green-300 text-green-800 text-sm';
                    messageDiv.textContent = '✓ Upload complete. Updating your list...';
                    messageDiv.classList.remove('hidden');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Upload failed. Please try again.');
                }
            } catch (err) {
                messageDiv.className = 'p-3 rounded-lg bg-red-100 border border-red-300 text-red-800 text-sm';
                messageDiv.textContent = '✗ ' + err.message;
                messageDiv.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });

        async function downloadDocument(docId) {
            try {
                const response = await fetch(`/documents/${docId}/download`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                const contentType = response.headers.get('content-type') || '';

                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    if (response.ok && data.download_url) {
                        window.location.href = data.download_url;
                        return;
                    }
                    throw new Error(data.error || 'Unable to download');
                }

                if (!response.ok) {
                    throw new Error('Unable to download');
                }

                const blob = await response.blob();
                const disposition = response.headers.get('Content-Disposition') || '';
                const fileNameMatch = disposition.match(/filename="?([^";]+)"?/i);
                const downloadName = fileNameMatch ? fileNameMatch[1] : 'document';

                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = downloadName;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            } catch (err) {
                alert('Error downloading document: ' + err.message);
            }
        }

        // Chat modal logic
        const csrfToken = '{{ csrf_token() }}';
        let activeDocumentId = null;
        let chatPoller = null;

        function getChatRefs() {
            return {
                chatModal: document.getElementById('chatModal'),
                chatTitle: document.getElementById('chatTitle'),
                chatList: document.getElementById('chatList'),
                chatForm: document.getElementById('chatForm'),
                chatMessageInput: document.getElementById('chatMessage'),
            };
        }

        async function openChatModal(docId, docLabel) {
            const {
                chatModal,
                chatTitle,
                chatMessageInput
            } = getChatRefs();
            activeDocumentId = docId;
            chatTitle.textContent = `Questions about: ${docLabel}`;
            chatMessageInput.value = '';
            await loadMessages();
            chatModal.classList.remove('hidden');

            if (chatPoller) clearInterval(chatPoller);
            chatPoller = setInterval(loadMessages, 4000);
        }

        function closeChatModal() {
            const {
                chatModal,
                chatList
            } = getChatRefs();
            chatModal.classList.add('hidden');
            chatList.innerHTML = '';
            activeDocumentId = null;
            if (chatPoller) {
                clearInterval(chatPoller);
                chatPoller = null;
            }
        }

        async function loadMessages() {
            const {
                chatList
            } = getChatRefs();
            if (!activeDocumentId) return;
            chatList.innerHTML = '<p class="text-sm text-gray-500">Loading messages...</p>';
            try {
                const res = await fetch(`/documents/${activeDocumentId}/messages`);
                if (!res.ok) throw new Error('Unable to load messages');
                const data = await res.json();
                if (!data.messages) throw new Error('No messages available');
                chatList.innerHTML = '';
                data.messages.forEach(msg => {
                    const bubble = document.createElement('div');
                    const isSelf = msg.user_id === {{ auth()->id() }};
                    bubble.className = `mb-2 flex ${isSelf ? 'justify-end' : 'justify-start'}`;
                    bubble.innerHTML = `
                        <div class="max-w-xs px-3 py-2 rounded-lg text-sm ${isSelf ? 'bg-teal-700 text-white' : 'bg-gray-100 text-gray-800'}">
                            <p class="font-semibold text-xs mb-1">${msg.user?.name ?? 'User'}</p>
                            <p>${msg.message}</p>
                            <p class="text-[10px] mt-1 opacity-75">${new Date(msg.created_at).toLocaleTimeString()}</p>
                        </div>
                    `;
                    chatList.appendChild(bubble);
                });
                chatList.scrollTop = chatList.scrollHeight;
            } catch (err) {
                chatList.innerHTML = `<p class="text-sm text-red-600">Unable to load messages right now.</p>`;
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            const {
                chatForm
            } = getChatRefs();
            if (!chatForm) return;
            chatForm.addEventListener('submit', async (e) => {
                const {
                    chatForm,
                    chatMessageInput
                } = getChatRefs();
                e.preventDefault();
                if (!activeDocumentId) return;
                const message = chatMessageInput.value.trim();
                if (!message) return;
                chatForm.querySelector('button').disabled = true;
                try {
                    const res = await fetch(`/documents/${activeDocumentId}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            message
                        })
                    });
                    if (!res.ok) throw new Error('Failed to send');
                    chatMessageInput.value = '';
                    await loadMessages();
                } catch (err) {
                    alert('Unable to send message right now. Please try again.');
                } finally {
                    chatForm.querySelector('button').disabled = false;
                }
            });
        });

        window.addEventListener('beforeunload', () => {
            if (chatPoller) clearInterval(chatPoller);
        });

        window.openChatModal = openChatModal;
        window.closeChatModal = closeChatModal;

        let dashboardSignature = '{{ $dashboardSignature ?? '0' }}';

        setInterval(async () => {
            if (document.hidden) return;

            try {
                const res = await fetch('{{ route('dashboard.heartbeat') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!res.ok) return;

                const data = await res.json();
                if (String(data.signature ?? '0') !== String(dashboardSignature)) {
                    window.location.reload();
                }
            } catch (error) {
            }
        }, 8000);
    </script>

    <!-- Chat Modal -->
    <div id="chatModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900" id="chatTitle">Document Questions</h3>
                <button onclick="closeChatModal()" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <div class="px-6 py-4 max-h-80 overflow-y-auto" id="chatList"></div>
            <div class="px-6 py-4 border-t">
                <div id="chatForm" class="flex gap-2">
                    <input type="text" id="chatMessage"
                        class="flex-1 border border-teal-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-600 focus:border-teal-600"
                        placeholder="Type your question..." required>
                    <button type="button"
                        onclick="document.getElementById('chatForm').dispatchEvent(new Event('submit'))"
                        class="bg-teal-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-teal-800">Send</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
