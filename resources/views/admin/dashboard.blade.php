<x-admin-layout>
    @section('header', 'Dashboard')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <!-- Users Card -->
        <div class="relative group bg-white rounded-4xl shadow-sm border border-gray-100 p-8 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:-translate-y-1 border-l-4 border-l-indigo-500">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em]">Total Platform Users</h3>
                    <p class="text-4xl font-black text-slate-900 mt-3 tracking-tight">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="p-4 bg-indigo-50 rounded-2xl transition-colors group-hover:bg-indigo-100">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-8 flex items-center text-slate-400">
                <div class="flex -space-x-2 mr-3 opacity-60">
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-indigo-400"></div>
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-indigo-300"></div>
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-indigo-200"></div>
                </div>
                <span class="text-xs font-bold uppercase tracking-wider">Growing community</span>
            </div>
        </div>

        <!-- Services Card -->
        <div class="relative group bg-white rounded-4xl shadow-sm border border-gray-100 p-8 transition-all duration-300 hover:shadow-xl hover:shadow-rose-500/5 hover:-translate-y-1 border-l-4 border-l-rose-500">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em]">Active Market Services</h3>
                    <p class="text-4xl font-black text-slate-900 mt-3 tracking-tight">{{ number_format($totalServices) }}</p>
                </div>
                <div class="p-4 bg-rose-50 rounded-2xl transition-colors group-hover:bg-rose-100">
                    <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
             <div class="mt-8 flex items-center text-slate-400">
                <div class="p-1 px-2.5 bg-emerald-50 text-emerald-600 rounded-lg mr-2 uppercase text-[10px] font-black tracking-widest">
                    Verified
                </div>
                <span class="text-xs font-bold uppercase tracking-wider">Secure Marketplace</span>
            </div>
        </div>

        <!-- Admins Card -->
         <div class="relative group bg-white rounded-4xl shadow-sm border border-gray-100 p-8 transition-all duration-300 hover:shadow-xl hover:shadow-amber-500/5 hover:-translate-y-1 border-l-4 border-l-amber-500">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em]">Management Staff</h3>
                    <p class="text-4xl font-black text-slate-900 mt-3 tracking-tight">{{ number_format($totalAdmins) }}</p>
                </div>
                <div class="p-4 bg-amber-50 rounded-2xl transition-colors group-hover:bg-amber-100">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
             <div class="mt-8 flex items-center text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider opacity-80">System Administrators</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 bg-white dark:bg-slate-800 rounded-4xl shadow-sm border border-gray-100 dark:border-slate-700 p-10">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-2xl font-black text-[#0F172A] dark:text-white tracking-tight">Quick Operations</h3>
                    <p class="text-sm text-gray-400 font-medium">Frequently accessed management actions</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('admin.users.index') }}" class="group block p-6 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-3xl transition-all duration-300 hover:border-indigo-100 hover:bg-white hover:shadow-xl hover:shadow-indigo-500/5">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-indigo-100 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <div class="font-bold text-gray-900 dark:text-white mb-1 text-lg">Manage Platform Users</div>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed">Oversee registered clients and providers, handle verification and profile audits.</p>
                </a>

                <a href="{{ route('admin.services.index') }}" class="group block p-6 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-3xl transition-all duration-300 hover:border-pink-100 hover:bg-white hover:shadow-xl hover:shadow-pink-500/5">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-pink-100 text-pink-600 rounded-2xl group-hover:bg-pink-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>
                    <div class="font-bold text-gray-900 dark:text-white mb-1 text-lg">Service Market Review</div>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed">Approve or reject service listings, manage categories, and ensure service quality compliance.</p>
                </a>
            </div>
        </div>

        <div class="lg:col-span-4 bg-[#0F172A] rounded-4xl shadow-2xl p-10 text-white relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-indigo-600/20 rounded-full blur-[100px]"></div>
            <div class="relative z-10">
                <h3 class="text-xl font-black mb-6 tracking-tight">System Status</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-sm font-bold">Server Load</span>
                        <span class="text-emerald-400 text-sm font-black italic underline tracking-widest uppercase">Optimal</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-500 h-full w-[15%]" style="width: 15%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-4">
                        <span class="text-slate-400 text-sm font-bold">Storage Usage</span>
                        <span class="text-indigo-400 text-sm font-black italic underline tracking-widest uppercase">42% Capacity</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-full w-[42%]" style="width: 42%"></div>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-white/5 backdrop-blur-md rounded-3xl border border-white/10">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Developer Insight</p>
                    <p class="text-xs text-slate-300 leading-relaxed font-medium">All systems are operational. Scheduled maintenance in 14 days.</p>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
