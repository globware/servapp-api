<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex bg-gray-50/50" x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0F172A] text-slate-300 transition-all duration-300 ease-in-out transform lg:translate-x-0 lg:static lg:inset-auto shadow-2xl"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            <div class="flex items-center px-8 h-20 bg-[#1E293B]/50 backdrop-blur-sm border-b border-slate-800/50">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('logo.jpeg') }}" alt="Logo" class="h-8 w-auto brightness-125 rounded-lg">
                    <span class="text-xl font-bold tracking-tight text-white uppercase italic">ServeApp</span>
                </div>
            </div>
            
            <nav class="mt-8 px-4 space-y-1.5 overflow-y-auto h-[calc(100vh-5rem)] custom-scrollbar">
                <div class="px-4 mb-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Core Management</p>
                </div>
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'hover:bg-slate-800/50 hover:text-white' }}">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-slate-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                    Dashboard
                </a>
                
                <a href="{{ route('admin.users.index') }}" 
                   class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'hover:bg-slate-800/50 hover:text-white' }}">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-slate-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    Users Management
                </a>
                
                <a href="{{ route('admin.services.index') }}" 
                   class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.services.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'hover:bg-slate-800/50 hover:text-white' }}">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 transition-colors {{ request()->routeIs('admin.services.*') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-slate-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    Market Services
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <header class="flex items-center justify-between h-24 px-8 bg-white/80 backdrop-blur-md border-b border-gray-100 z-40 sticky top-0">
                <div class="flex items-center min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 mr-4 text-gray-500 hover:bg-gray-100 rounded-lg focus:outline-none lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="truncate">
                        @if(trim($__env->yieldContent('breadcrumbs')))
                            @yield('breadcrumbs')
                        @else
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-1.5 opacity-60">
                                Administration / @yield('header')
                            </p>
                        @endif
                        <h2 class="text-xl md:text-2xl font-black text-[#0F172A] tracking-tight truncate leading-none">@yield('header')</h2>
                    </div>
                </div>
                
                <div class="flex items-center space-x-5">
                    @yield('actions')
                    <!-- Notifications placeholder -->
                    <button class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v1m6 0H9"></path>
                        </svg>
                    </button>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 p-1.5 pr-3 hover:bg-gray-100 rounded-2xl transition-all duration-200 focus:outline-none">
                            <div class="w-9 h-9 bg-indigo-100 flex items-center justify-center rounded-xl text-indigo-700 font-bold border-2 border-white shadow-sm">
                                {{ strtoupper(substr(Auth::guard('web')->user()->firstname ?? 'A', 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-bold text-gray-900 leading-tight">{{ Auth::guard('web')->user()->firstname ?? 'Admin' }}</p>
                                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Super Admin</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" style="display: none;" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="origin-top-right absolute right-0 mt-3 w-56 rounded-2xl shadow-2xl py-2 bg-white ring-1 ring-black/5 focus:outline-none z-50">
                            <div class="px-4 py-3 border-b border-gray-50">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Account Details</p>
                                <p class="text-sm font-bold text-gray-900 mt-1">{{ Auth::guard('web')->user()->email ?? 'admin@serveapp.com' }}</p>
                            </div>
                            <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                My Profile
                            </a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm font-bold text-rose-500 hover:bg-rose-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/10 p-8">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-sm animate-fade-in-down" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="font-bold text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl shadow-sm animate-fade-in-down" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            <span class="font-bold text-sm">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                
                {{ $slot }}
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
