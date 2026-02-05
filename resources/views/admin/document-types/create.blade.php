<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Create Document Type</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Form Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Document Type</h3>
                    <p class="text-sm text-gray-600 mt-1">Create a new document type that clients need to submit</p>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <form action="{{ route('admin.document-types.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Document Type Name -->
                        <div>
                            <label for="doc_type" class="block text-sm font-semibold text-gray-900 mb-2">
                                Document Type Name <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="doc_type" name="doc_type" value="{{ old('doc_type') }}" 
                                placeholder="e.g., Government ID, Proof of Address" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all">
                            @error('doc_type')
                                <p class="text-red-600 text-sm mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">Description</label>
                            <textarea id="description" name="description" rows="4" 
                                placeholder="Enter a brief description of this document type..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all resize-none">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Checkboxes Row -->
                        <div class="grid grid-cols-2 gap-6">
                            <div class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:bg-blue-50 transition-colors">
                                <input type="checkbox" id="required" name="required" value="1" {{ old('required') ? 'checked' : '' }}
                                    class="w-5 h-5 mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                <div>
                                    <label for="required" class="text-sm font-semibold text-gray-900 cursor-pointer">Mark as Required</label>
                                    <p class="text-xs text-gray-600 mt-1">Clients must submit this document</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 transition-colors">
                                <input type="checkbox" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                                    class="w-5 h-5 mt-0.5 rounded border-gray-300 text-green-600 focus:ring-2 focus:ring-green-500 cursor-pointer">
                                <div>
                                    <label for="active" class="text-sm font-semibold text-gray-900 cursor-pointer">Active</label>
                                    <p class="text-xs text-gray-600 mt-1">Make this document type available</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-semibold text-gray-900 mb-2">Sort Order</label>
                            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
                                placeholder="Enter the display order (0-indexed)"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all">
                            @error('sort_order')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex gap-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Document Type
                            </button>
                            <a href="{{ route('admin.document-types.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2.5 rounded-lg font-semibold transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
