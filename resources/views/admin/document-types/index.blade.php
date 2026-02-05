<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Document Type Management</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Success!</p>
                        <p class="text-sm">{{ $message }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Document Types</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage the document types required from clients</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-semibold text-sm">
                            {{ count($documentTypes) }} Types
                        </div>
                        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Document Type
                        </button>
                    </div>
                </div>

                @if ($documentTypes->isEmpty())
                    <div class="text-center py-20 px-6">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium mb-2">No document types found</p>
                        <p class="text-gray-400 text-sm mb-6">Get started by creating your first document type</p>
                        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-semibold transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create First Type
                        </button>
                    </div>
                @else
                    <!-- Bootstrap-style Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">S.No</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Document Type</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Required</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Active</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Order</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($documentTypes as $index => $type)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-semibold text-gray-900">{{ str_replace('_', ' ', $type->doc_type) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                            {{ $type->description ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($type->required)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Yes
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    No
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($type->active)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                                                    <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">
                                                    <span class="w-2 h-2 bg-red-600 rounded-full"></span>
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded border border-gray-300 bg-gray-50 text-gray-700 text-xs font-bold">{{ $type->sort_order }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2">
                                                <button onclick="editDocumentType({{ $type->id }}, '{{ str_replace("'", "\\'", $type->doc_type) }}', '{{ str_replace("'", "\\'", $type->description ?? '') }}', {{ $type->required ? 'true' : 'false' }}, {{ $type->active ? 'true' : 'false' }}, {{ $type->sort_order }})" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-indigo-600 hover:bg-indigo-50 rounded transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button onclick="deleteDocumentType({{ $type->id }}, '{{ str_replace("'", "\\'", $type->doc_type) }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-red-600 hover:bg-red-50 rounded transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add/Edit Document Type Modal -->
    <div id="documentTypeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 border-b border-blue-200">
                <h3 id="modalTitle" class="text-lg font-bold text-white">Add Document Type</h3>
            </div>

            <!-- Modal Body -->
            <form id="documentTypeForm" class="p-6 space-y-4">
                <input type="hidden" id="documentTypeId" name="id" value="">

                <!-- Document Type Name -->
                <div>
                    <label for="modal_doc_type" class="block text-sm font-semibold text-gray-900 mb-2">
                        Document Type Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="modal_doc_type" name="doc_type" placeholder="e.g., Government ID, Passport"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                    <p id="doc_type_error" class="text-red-600 text-sm mt-1 hidden flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span id="doc_type_error_text"></span>
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label for="modal_description" class="block text-sm font-semibold text-gray-900 mb-2">Description</label>
                    <textarea id="modal_description" name="description" rows="3" placeholder="Enter a brief description..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all resize-none"></textarea>
                    <p id="description_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>

                <!-- Checkboxes -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors">
                        <input type="checkbox" id="modal_required" name="required" value="1"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <span class="text-sm font-semibold text-gray-900">Required</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition-colors">
                        <input type="checkbox" id="modal_active" name="active" value="1"
                            class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-2 focus:ring-green-500">
                        <span class="text-sm font-semibold text-gray-900">Active</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="modal_sort_order" class="block text-sm font-semibold text-gray-900 mb-2">Sort Order</label>
                    <input type="number" id="modal_sort_order" name="sort_order" placeholder="0" min="0"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all">
                    <p id="sort_order_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="flex gap-3 p-6 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitDocumentTypeForm()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold transition-all">
                    <span id="submitBtnText">Save</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Document Type';
            document.getElementById('submitBtnText').textContent = 'Create';
            document.getElementById('documentTypeForm').reset();
            document.getElementById('documentTypeId').value = '';
            clearErrors();
            document.getElementById('documentTypeModal').classList.remove('hidden');
        }

        function editDocumentType(id, docType, description, required, active, sortOrder) {
            document.getElementById('modalTitle').textContent = 'Edit Document Type';
            document.getElementById('submitBtnText').textContent = 'Update';
            document.getElementById('documentTypeId').value = id;
            document.getElementById('modal_doc_type').value = docType;
            document.getElementById('modal_description').value = description;
            document.getElementById('modal_required').checked = required;
            document.getElementById('modal_active').checked = active;
            document.getElementById('modal_sort_order').value = sortOrder;
            clearErrors();
            document.getElementById('documentTypeModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('documentTypeModal').classList.add('hidden');
            document.getElementById('documentTypeForm').reset();
        }

        function clearErrors() {
            document.querySelectorAll('[id$="_error"]').forEach(el => {
                el.classList.add('hidden');
                if (el.id !== 'doc_type_error') {
                    el.textContent = '';
                }
            });
        }

        function submitDocumentTypeForm() {
            const id = document.getElementById('documentTypeId').value;
            const docType = document.getElementById('modal_doc_type').value.trim();
            const description = document.getElementById('modal_description').value.trim();
            const required = document.getElementById('modal_required').checked ? 1 : 0;
            const active = document.getElementById('modal_active').checked ? 1 : 0;
            const sortOrder = document.getElementById('modal_sort_order').value || 0;

            const url = id ? `/admin/document-types/${id}` : '/admin/document-types';
            const method = id ? 'PUT' : 'POST';

            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('_method', method);
            formData.append('doc_type', docType);
            formData.append('description', description);
            formData.append('required', required);
            formData.append('active', active);
            formData.append('sort_order', sortOrder);

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else if (data.errors) {
                    clearErrors();
                    Object.keys(data.errors).forEach(field => {
                        const errorEl = document.getElementById(field + '_error');
                        if (errorEl) {
                            if (field === 'doc_type') {
                                document.getElementById('doc_type_error_text').textContent = data.errors[field][0];
                            } else {
                                errorEl.textContent = data.errors[field][0];
                            }
                            errorEl.classList.remove('hidden');
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function deleteDocumentType(id, docType) {
            if (confirm(`Are you sure you want to delete "${docType}"?`)) {
                fetch(`/admin/document-types/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Close modal when clicking outside
        document.getElementById('documentTypeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Keyboard shortcut to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</x-app-layout>
