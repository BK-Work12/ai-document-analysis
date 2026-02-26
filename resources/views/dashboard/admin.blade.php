<x-app-layout>
    <x-slot name="header">Admin Dashboard</x-slot>

    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-teal-900">Overview</h2>
                <p class="mt-1 text-sm text-teal-700">Track client activity and review document progress from one place.</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
                <div class="rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-600 p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium mb-1">Total Clients</p>
                            <p class="text-4xl font-bold">{{ $stats['total_clients'] }}</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-cyan-600 to-teal-700 p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-cyan-100 text-sm font-medium mb-1">Awaiting Review</p>
                            <p class="text-4xl font-bold">{{ $stats['pending_docs'] }}</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm font-medium mb-1">Action Needed</p>
                            <p class="text-4xl font-bold">{{ $stats['needs_correction'] }}</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-emerald-600 to-green-700 p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium mb-1">Approved</p>
                            <p class="text-4xl font-bold">{{ $stats['approved'] }}</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clients Table -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-700 to-emerald-600">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-teal-900">Client List</h3>
                            <p class="text-sm text-teal-700">Open a client profile to review documents and messages.</p>
                        </div>
                    </div>
                    
                    @if($clients->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-gray-600">No clients registered yet.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-emerald-50/60">
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-teal-700">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-teal-700">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-teal-700">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-teal-700">Joined</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-teal-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach($clients as $client)
                                        <tr class="transition hover:bg-slate-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-teal-600 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                        {{ strtoupper(substr($client->name, 0, 1)) }}
                                                    </div>
                                                    <span class="font-medium text-teal-900">{{ $client->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-600">{{ $client->email }}</td>
                                            <td class="px-6 py-4">
                                                @if($client->email_verified_at)
                                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">✓ Verified</span>
                                                @else
                                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">⏳ Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-600">{{ $client->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('admin.clients.show', $client) }}" class="inline-flex items-center rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Open Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
    </div>
</x-app-layout>
