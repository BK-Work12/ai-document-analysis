<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Completeness') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Your profile status overview.') }}
        </p>
    </header>

    <div class="mt-6">
        <!-- Completion Status -->
        <div class="mb-6 bg-gradient-to-br from-blue-50 to-indigo-50 overflow-hidden rounded-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Required Documents</h3>
                <span class="text-2xl font-bold text-blue-600">{{ $completionPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $completionPercentage }}%"></div>
            </div>
            <p class="text-sm text-gray-700">{{ $approvedDocuments }} of {{ $totalRequired }} required documents approved</p>
        </div>

        <!-- Documents List -->
        <div class="space-y-3">
            @foreach($requirements as $req)
                @php
                    $doc = $documents->where('doc_type', $req->doc_type)->first();
                    $isApproved = $doc && $doc->status === 'approved';
                    $isPending = $doc && $doc->status === 'pending';
                @endphp
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                    <div class="flex items-center flex-1">
                        <div class="mr-4">
                            @if($isApproved)
                                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($isPending)
                                <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $req->doc_type)) }}</p>
                            <p class="text-sm text-gray-600">{{ $req->description }}</p>
                            @if($doc)
                                <p class="text-xs text-gray-500 mt-1">
                                    @if($isApproved)
                                        <span class="text-green-600 font-medium">✓ Approved</span>
                                    @elseif($isPending)
                                        <span class="text-yellow-600 font-medium">⧖ Under Review</span>
                                    @else
                                        <span class="text-red-600 font-medium">✗ Rejected</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4">
                        @if($isApproved)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Approved</span>
                        @elseif($isPending)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Pending</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Not Uploaded</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
