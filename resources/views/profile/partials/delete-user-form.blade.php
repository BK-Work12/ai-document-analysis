<section class="space-y-6">
    <!-- Warning Message -->
    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-red-800">
                <p class="font-bold mb-2">This action cannot be undone!</p>
                <p>Once your account is deleted, all of its resources and data will be permanently removed from our servers. This includes:</p>
                <ul class="list-disc list-inside mt-2 space-y-1 text-xs">
                    <li>All uploaded documents</li>
                    <li>Chat history and messages</li>
                    <li>Profile information</li>
                    <li>Account settings and preferences</li>
                </ul>
                <p class="mt-2 font-semibold">Please download any data you wish to retain before proceeding.</p>
            </div>
        </div>
    </div>

    <!-- Delete Button -->
    <div class="flex justify-end">
        <button type="button" onclick="openDeleteModal()" 
            class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Delete My Account
        </button>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 border-b">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-white">Confirm Account Deletion</h3>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <div class="space-y-4">
                    <p class="text-gray-900 font-semibold">Are you absolutely sure?</p>
                    <p class="text-sm text-gray-600">
                        This will permanently delete your account and all associated data. This action cannot be reversed.
                    </p>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="delete_password" class="block text-sm font-semibold text-gray-900 mb-2">
                            Enter your password to confirm <span class="text-red-600">*</span>
                        </label>
                        <input id="delete_password" name="password" type="password" 
                            placeholder="Enter your current password"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900 transition-all" required>
                        @error('password', 'userDeletion')
                            <p class="text-red-600 text-sm mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirmation Checkbox -->
                    <label class="flex items-start gap-3 p-3 border border-red-200 rounded-lg bg-red-50 cursor-pointer">
                        <input type="checkbox" id="confirm_delete" required
                            class="w-4 h-4 mt-0.5 rounded border-red-300 text-red-600 focus:ring-2 focus:ring-red-500">
                        <span class="text-sm text-red-800">
                            <span class="font-semibold">I understand that this action is permanent</span> and I want to delete my account along with all my data.
                        </span>
                    </label>
                </div>

                <!-- Modal Footer -->
                <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2.5 border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 rounded-lg font-semibold transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-semibold transition-all">
                        Yes, Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            document.getElementById('deleteAccountModal').classList.remove('hidden');
            document.getElementById('delete_password').focus();
        }

        function closeDeleteModal() {
            document.getElementById('deleteAccountModal').classList.add('hidden');
            document.getElementById('delete_password').value = '';
            document.getElementById('confirm_delete').checked = false;
        }

        // Close modal when clicking outside
        document.getElementById('deleteAccountModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Show modal if there are validation errors
        @if($errors->userDeletion->isNotEmpty())
            openDeleteModal();
        @endif
    </script>
</section>
