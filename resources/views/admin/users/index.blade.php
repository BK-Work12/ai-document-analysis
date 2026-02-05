<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 00-6-6 6 6 0 00-6 6z"></path>
            </svg>
            <div>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight">User Management</h2>
                <p class="text-sm text-gray-600 mt-0.5">Manage application users and their roles</p>
            </div>
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

            @if ($message = Session::get('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Error!</p>
                        <p class="text-sm">{{ $message }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">All Users</h3>
                        <p class="text-sm text-gray-600 mt-1">View and manage system users</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-semibold text-sm">
                            {{ count($users) }} Users
                        </div>
                        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add User
                        </button>
                    </div>
                </div>

                @if ($users->isEmpty())
                    <div class="text-center py-20 px-6">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 00-6-6 6 6 0 00-6 6z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium mb-2">No users found</p>
                        <p class="text-gray-400 text-sm mb-6">Get started by creating your first user</p>
                        <button onclick="openAddModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-semibold transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create First User
                        </button>
                    </div>
                @else
                    <!-- Bootstrap-style Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">S.No</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($users as $index => $user)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <span class="text-sm font-semibold text-gray-900">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-600">{{ $user->email }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->role === 'admin')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                                                    </svg>
                                                    Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 11a6 6 0 00-5.86 0A.5.5 0 005 16h8a.5.5 0 00-1.07-5z"></path>
                                                    </svg>
                                                    Client
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.users.documents', $user->id) }}" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                                    title="View Documents & Messages">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                                    </svg>
                                                    Messages
                                                </a>
                                                <button onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-indigo-600 hover:bg-indigo-50 rounded transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')"
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

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 border-b border-blue-200">
                <h3 id="modalTitle" class="text-lg font-bold text-white">Add User</h3>
            </div>

            <!-- Modal Body -->
            <form id="userForm" class="p-6 space-y-4">
                <input type="hidden" id="userId" name="id" value="">

                <!-- Name -->
                <div>
                    <label for="modal_name" class="block text-sm font-semibold text-gray-900 mb-2">
                        Full Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="modal_name" name="name" placeholder="e.g., John Doe"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                    <p id="name_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>

                <!-- Email -->
                <div>
                    <label for="modal_email" class="block text-sm font-semibold text-gray-900 mb-2">
                        Email Address <span class="text-red-600">*</span>
                    </label>
                    <input type="email" id="modal_email" name="email" placeholder="e.g., user@example.com"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                    <p id="email_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>

                <!-- Password -->
                <div>
                    <label for="modal_password" class="block text-sm font-semibold text-gray-900 mb-2">
                        Password <span class="text-red-600" id="passwordRequired">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="modal_password" name="password" placeholder="Minimum 8 characters"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                        <button type="button" onclick="togglePasswordVisibility('modal_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <p id="password_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="modal_password_confirmation" class="block text-sm font-semibold text-gray-900 mb-2">
                        Confirm Password <span class="text-red-600" id="confirmRequired">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="modal_password_confirmation" name="password_confirmation" placeholder="Confirm password"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                        <button type="button" onclick="togglePasswordVisibility('modal_password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <p id="password_confirmation_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>

                <!-- Role -->
                <div>
                    <label for="modal_role" class="block text-sm font-semibold text-gray-900 mb-2">
                        User Role <span class="text-red-600">*</span>
                    </label>
                    <select id="modal_role" name="role" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                        <option value="">Select a role</option>
                        <option value="admin">Administrator</option>
                        <option value="client">Client</option>
                    </select>
                    <p id="role_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="flex gap-3 p-6 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitUserForm()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold transition-all">
                    <span id="submitBtnText">Save</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add User';
            document.getElementById('submitBtnText').textContent = 'Create';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('modal_password').required = true;
            document.getElementById('modal_password_confirmation').required = true;
            document.getElementById('passwordRequired').textContent = '*';
            document.getElementById('confirmRequired').textContent = '*';
            clearErrors();
            document.getElementById('userModal').classList.remove('hidden');
        }

        function editUser(id, name, email, role) {
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('submitBtnText').textContent = 'Update';
            document.getElementById('userId').value = id;
            document.getElementById('modal_name').value = name;
            document.getElementById('modal_email').value = email;
            document.getElementById('modal_role').value = role;
            document.getElementById('modal_password').value = '';
            document.getElementById('modal_password_confirmation').value = '';
            document.getElementById('modal_password').required = false;
            document.getElementById('modal_password_confirmation').required = false;
            document.getElementById('passwordRequired').textContent = '';
            document.getElementById('confirmRequired').textContent = '';
            clearErrors();
            document.getElementById('userModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.getElementById('userForm').reset();
        }

        function clearErrors() {
            document.querySelectorAll('[id$="_error"]').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
        }

        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }

        function submitUserForm() {
            const id = document.getElementById('userId').value;
            const name = document.getElementById('modal_name').value.trim();
            const email = document.getElementById('modal_email').value.trim();
            const password = document.getElementById('modal_password').value;
            const passwordConfirmation = document.getElementById('modal_password_confirmation').value;
            const role = document.getElementById('modal_role').value;

            const url = id ? `/admin/users/${id}` : '/admin/users';
            const method = id ? 'PUT' : 'POST';

            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('_method', method);
            formData.append('name', name);
            formData.append('email', email);
            if (password) {
                formData.append('password', password);
                formData.append('password_confirmation', passwordConfirmation);
            }
            formData.append('role', role);

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
                            errorEl.textContent = data.errors[field][0];
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

        function deleteUser(id, name) {
            if (confirm(`Are you sure you want to delete the user "${name}"?`)) {
                fetch(`/admin/users/${id}`, {
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
                    } else {
                        alert(data.message || 'Error deleting user');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Close modal when clicking outside
        document.getElementById('userModal').addEventListener('click', function(e) {
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
