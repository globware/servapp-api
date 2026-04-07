<x-admin-layout>
    @section('header', 'Users Management')

    @section('actions')
        <button class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-sm text-xs font-black rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add New User
        </button>
    @endsection

    <div class="space-y-6">
        <!-- Header Actions & filters -->
        <div class="bg-white rounded-4xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                        class="block w-full pl-12 pr-4 py-3 border border-gray-100 rounded-2xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 sm:text-sm transition-all duration-200" 
                        placeholder="Search by name, email or phone...">
                </div>
                
                <div class="flex items-center gap-3">
                    <select class="block w-full pl-4 pr-10 py-3 text-sm border-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 rounded-2xl bg-gray-50 transition-all duration-200 font-bold text-slate-600">
                        <option>All Status</option>
                        <option>Verified</option>
                        <option>Unverified</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-4xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                User Identity
                            </th>
                             <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Contact Info
                            </th>
                            <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Registered
                            </th>
                            <th scope="col" class="px-8 py-5 text-right text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Operations
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/30 transition-colors duration-200">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black border border-indigo-100 shrink-0 shadow-sm">
                                             @if($user->avatar)
                                                <img class="h-12 w-12 rounded-2xl object-cover" src="{{ $user->avatar }}" alt="">
                                             @else
                                                <span class="text-lg">{{ strtoupper(substr($user->firstname, 0, 1)) }}{{ strtoupper(substr($user->lastname, 0, 1)) }}</span>
                                             @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-black text-slate-900 leading-none mb-1.5">
                                                {{ $user->firstname }} {{ $user->lastname }}
                                            </div>
                                            <div class="flex items-center">
                                                @if($user->is_verified)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100/50">
                                                        Verified
                                                    </span>
                                                @else
                                                     <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-slate-100 text-slate-400 border border-slate-200/50">
                                                        Unverified
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-700 leading-none mb-1">{{ $user->email }}</div>
                                    <div class="text-[10px] text-slate-400 font-black tracking-tight uppercase">{{ $user->phone }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm text-slate-500 font-black">
                                    <span class="text-slate-900 text-xs">{{ $user->created_at->format('M d, Y') }}</span>
                                    <div class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter opacity-70">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-black">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-2xl transition-all duration-200 border border-transparent hover:border-indigo-100 shadow-sm hover:shadow-indigo-500/5">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-2xl transition-all duration-200 border border-transparent hover:border-rose-100 shadow-sm hover:shadow-rose-500/5">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 bg-slate-50 rounded-[2.5rem] flex items-center justify-center text-slate-200 mb-6 border border-slate-100 shadow-inner">
                                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-slate-900 text-lg font-black tracking-tight">No Management Records</p>
                                        <p class="text-slate-400 text-xs mt-2 font-bold uppercase tracking-widest opacity-60">Try adjusting your search or filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-8 py-6 bg-slate-50/50 border-t border-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
