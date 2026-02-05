<!-- Sidebar Navigation -->
<div x-data="{ sidebarOpen: true }" class="flex min-h-screen h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="bg-gradient-to-b from-slate-900 to-slate-800 text-white transition-all duration-300 fixed inset-y-0 left-0 flex flex-col shadow-lg z-50 w-64"
         x-bind:style="sidebarOpen ? 'width: 256px;' : 'width: 80px;'">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-slate-700">
            <div x-show="sidebarOpen" class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-lg font-bold whitespace-nowrap">DocAnalysis AI</span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="p-1.5 hover:bg-slate-700 rounded-lg transition flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-2">
            @if(Auth::user()->isClient())
                <!-- Client Navigation -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">My Documents</span>
                </a>

                <!-- Chats -->
                <a href="{{ route('chats.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('chats.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Chats</span>
                </a>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('profile.edit') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Profile</span>
                </a>

            @elseif(Auth::user()->isAdmin())
                <!-- Admin Navigation -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 16l4-4m0 0l4 4m-4-4v4m0-11l-4 4m4-4l4-4"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Dashboard</span>
                </a>

                <a href="{{ route('admin.chats.list') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.chats.list') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Chats</span>
                </a>

                <a href="{{ route('admin.document-types.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.document-types.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Document Types</span>
                </a>

                <a href="{{ route('admin.documents.chat') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.documents.chat') || request()->routeIs('admin.documents.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Document Conversations</span>
                </a>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 00-6-6 6 6 0 00-6 6z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Users</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Settings</span>
                </a>

                <a href="{{ route('admin.jobs.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.jobs.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7-4a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Job Tracking</span>
                </a>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('profile.edit') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Profile</span>
                </a>
            @endif
        </nav>

        <!-- User Profile Section -->
        <div class="border-t border-slate-700 p-4">
            <div x-data="{ dropdownOpen: false }" class="relative">
                <button @click="dropdownOpen = !dropdownOpen" class="w-full flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-slate-700 transition">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div x-show="sidebarOpen" class="text-left min-w-0">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->role }}</p>
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="dropdownOpen" 
                     @click.away="dropdownOpen = false"
                     class="absolute bottom-full left-0 w-full mb-2 bg-slate-700 rounded-lg shadow-lg overflow-hidden z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-slate-600 transition">
                        Edit Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-600 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col flex-1" x-bind:style="sidebarOpen ? 'margin-left: 256px;' : 'margin-left: 80px;'" style="margin-left: 256px;">
        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-40 shadow-sm">
            <div>
                @isset($header)
                    <h1 class="text-2xl font-bold text-gray-900">{{ $header }}</h1>
                @else
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                @endisset
            </div>
            <div class="flex items-center space-x-4" x-data="{ notificationsOpen: false }">
                <div class="relative">
                    <button @click="notificationsOpen = !notificationsOpen" class="relative inline-flex items-center p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="notificationsOpen" @click.away="notificationsOpen = false" 
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">
                        @forelse(Auth::user()->notifications->take(10) as $notification)
                            <div class="px-4 py-3 border-b {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 text-sm">{{ $notification->data['title'] ?? 'New Notification' }}</p>
                                        <p class="text-xs text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 100) }}</p>
                                        @php
                                            $docId = $notification->data['document_id'] ?? null;
                                            $userId = $notification->data['user_id'] ?? null;
                                            $chatUrl = null;
                                            if ($docId) {
                                                $chatUrl = Auth::user()->isAdmin()
                                                    ? route('admin.chats.show', [$userId, $docId])
                                                    : route('chats.show', $docId);
                                            }
                                        @endphp
                                        @if($chatUrl)
                                            <a href="{{ $chatUrl }}" class="text-xs text-blue-600 hover:text-blue-800 mt-2 inline-block">View Chat →</a>
                                        @endif
                                    </div>
                                    @if(!$notification->read_at)
                                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 ml-2">✓</button>
                                        </form>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-500">
                                <p class="text-sm">No notifications yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-auto bg-gray-50 w-full">
            {{ $slot }}
        </main>
    </div>
</div>

<style>
    [x-cloak] { display: block !important; }
    html, body { height: 100%; overflow: hidden; }
</style>
