<x-app-layout>
    <x-slot name="header">Job Tracking</x-slot>

    <div class="p-8 max-w-7xl mx-auto">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow-xl sm:rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">Total Jobs</p>
                        <p class="text-4xl font-bold">{{ $stats['total_jobs'] }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-pink-600 overflow-hidden shadow-xl sm:rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium mb-1">Failed Jobs</p>
                        <p class="text-4xl font-bold">{{ $stats['failed_jobs'] }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 overflow-hidden shadow-xl sm:rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">Processed Today</p>
                        <p class="text-4xl font-bold">{{ $stats['processed_today'] }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ $message }}
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ $message }}
            </div>
        @endif

        <!-- Active Jobs -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 text-white">
                <h2 class="text-xl font-bold">Active Jobs</h2>
            </div>
            <div class="overflow-x-auto">
                @if($jobs->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Queue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Job Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Attempts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($jobs as $job)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $job->queue }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if(isset($job->decoded_payload['displayName']))
                                            {{ $job->decoded_payload['displayName'] }}
                                        @else
                                            <span class="text-xs text-gray-400">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $job->attempts }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::createFromTimestamp($job->created_at)->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <details class="cursor-pointer">
                                            <summary class="text-blue-600 hover:text-blue-700 font-medium">View</summary>
                                            <div class="mt-2 p-3 bg-gray-50 rounded border border-gray-200">
                                                <pre class="text-xs overflow-auto max-h-48">{{ json_encode($job->decoded_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t bg-gray-50">
                        {{ $jobs->links() }}
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        No active jobs at the moment.
                    </div>
                @endif
            </div>
        </div>

        <!-- Failed Jobs -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-red-700 to-red-800 text-white flex justify-between items-center">
                <h2 class="text-xl font-bold">Failed Jobs</h2>
                @if($failedJobs->total() > 0)
                    <form action="{{ route('admin.jobs.clear-failed') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all failed jobs?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white text-red-700 rounded-lg font-medium hover:bg-gray-100 transition">
                            Clear All
                        </button>
                    </form>
                @endif
            </div>
            <div class="overflow-x-auto">
                @if($failedJobs->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Queue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Job Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Failed At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Error & Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($failedJobs as $job)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $job->queue ?? 'default' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if(isset($job->decoded_payload['displayName']))
                                            {{ $job->decoded_payload['displayName'] }}
                                        @else
                                            <span class="text-xs text-gray-400">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::createFromTimestamp($job->failed_at)->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <details class="cursor-pointer">
                                            <summary class="text-red-600 hover:text-red-700 font-medium">View Error</summary>
                                            <div class="mt-2 p-3 bg-red-50 rounded border border-red-200 max-w-2xl">
                                                <p class="text-xs font-semibold text-red-700 mb-2">Exception:</p>
                                                <pre class="text-xs overflow-auto max-h-48 bg-white p-2 rounded border border-red-200">{{ $job->exception }}</pre>
                                                @if(isset($job->decoded_payload))
                                                    <p class="text-xs font-semibold text-red-700 mb-2 mt-3">Job Payload:</p>
                                                    <pre class="text-xs overflow-auto max-h-48 bg-white p-2 rounded border border-red-200">{{ json_encode($job->decoded_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                @endif
                                            </div>
                                        </details>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <form action="{{ route('admin.jobs.retry-failed', $job->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-medium">
                                                Retry
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t bg-gray-50">
                        {{ $failedJobs->links() }}
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        No failed jobs. Great job!
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
