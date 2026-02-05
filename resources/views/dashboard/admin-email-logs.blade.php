<x-app-layout>
    <x-slot name="header"></x-slot>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Email Logs - {{ $user->name }}</h1>
                <p class="mt-1 text-gray-600">{{ $user->email }}</p>
            </div>
            <a href="{{ route('admin.clients.show', $user) }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Client
            </a>
        </div>
    </div>

    <!-- Email Logs Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($logs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Retries</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $log->type)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $log->to }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($log->status === 'sent') bg-green-100 text-green-800
                                @elseif($log->status === 'queued') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $log->retry_count ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($log->error)
                            <button onclick="showError('{{ addslashes($log->error) }}')" class="text-red-600 hover:text-red-800 font-medium">
                                View Error
                            </button>
                            @else
                            <span class="text-gray-500">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-500">
            No email logs found for this client.
        </div>
        @endif
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Error Details</h3>
        </div>
        <div class="px-6 py-4">
            <p id="errorContent" class="text-sm text-gray-700 font-mono bg-gray-50 p-3 rounded break-words"></p>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button onclick="closeError()" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function showError(error) {
    document.getElementById('errorContent').textContent = error;
    document.getElementById('errorModal').classList.remove('hidden');
}

function closeError() {
    document.getElementById('errorModal').classList.add('hidden');
}
</script>

</x-app-layout>
