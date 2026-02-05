<x-app-layout>
    <x-slot name="header">All Chats</x-slot>

    <div class="p-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($users as $user)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden border border-gray-200">
                    <!-- User Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                        <h3 class="text-xl font-bold">{{ $user->name }}</h3>
                        <p class="text-sm text-blue-100 mt-1">{{ $user->email }}</p>
                    </div>

                    <!-- Documents List -->
                    <div class="p-6">
                        @if($user->documents()->count() === 0)
                            <p class="text-gray-500 text-sm">No documents submitted</p>
                        @else
                            <div class="space-y-3 mb-6">
                                @foreach($user->documents()->latest()->limit(5)->get() as $doc)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->original_filename }}</p>
                                            <p class="text-xs text-gray-600">{{ ucwords(str_replace('_', ' ', $doc->doc_type)) }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                {{ $doc->messages()->count() }}
                                            </span>
                                            <span class="text-xs font-semibold {{ 
                                                $doc->status === 'approved' ? 'bg-green-100 text-green-800' :
                                                ($doc->status === 'needs_correction' ? 'bg-red-100 text-red-800' :
                                                'bg-yellow-100 text-yellow-800')
                                            }} px-2 py-1 rounded">
                                                {{ $doc->status === 'approved' ? '✓' : ($doc->status === 'needs_correction' ? '✗' : '⊙') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <a href="{{ route('admin.chats.index', $user->id) }}" class="w-full block text-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                View Chats
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No clients found</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
