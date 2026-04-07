<x-admin-layout>
    @section('header', 'Market Services')

    <div class="space-y-6">
        <!-- Header Actions & Filters -->
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
                        placeholder="Search services, providers or tags...">
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <select class="block pl-4 pr-10 py-3 text-sm border-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 rounded-2xl bg-gray-50 transition-all duration-200 min-w-35 font-bold text-slate-600">
                        <option>All Types</option>
                        <option>Plumbing</option>
                        <option>Welding</option>
                    </select>
                    
                    <select class="block pl-4 pr-10 py-3 text-sm border-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 rounded-2xl bg-gray-50 transition-all duration-200 min-w-35 font-bold text-slate-600">
                        <option>Verification</option>
                        <option>Verified</option>
                        <option>Pending</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <div class="bg-white rounded-4xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-slate-50/50">
                        <tr>
                             <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Service Blueprint
                            </th>
                            <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Specifications
                            </th>
                            <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Regionality
                            </th>
                            <th scope="col" class="px-8 py-5 text-left text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Status
                            </th>
                            <th scope="col" class="px-8 py-5 text-right text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Operations
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($services as $service)
                            <tr class="hover:bg-gray-50/30 transition-colors duration-200">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="shrink-0 h-12 w-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-xl border border-indigo-100 shadow-sm">
                                            {{ strtoupper(substr($service->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div class="ml-4 tabular-nums">
                                            <div class="text-sm font-black text-slate-900 truncate max-w-50 leading-none mb-1.5">
                                                {{ $service->name ?? $service->getType() }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-black uppercase tracking-tight">
                                                By {{ $service->user->firstname ?? 'Unknown' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100/50">
                                         {{ $service->service->name ?? 'Standard' }}
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-2 font-black tracking-tight uppercase opacity-70">
                                        {{ $service->requests_count ?? 0 }} Engagement requests
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 leading-none mb-1">{{ $service->state->name ?? 'N/A' }}</span>
                                        <span class="text-[10px] text-slate-400 font-black tracking-widest uppercase">{{ $service->country->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if($service->verified)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100/50">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100/50">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-black" id="verify-cell-{{ $service->id }}">
                                    <div class="flex items-center justify-end space-x-2">
                                        <span id="verify-span-{{ $service->id }}">
                                            @if(!$service->verified)
                                                <button 
                                                    onclick="verifyService(this, '{{ $service->id }}')" 
                                                    data-url="{{ route('admin.services.verify', $service->id) }}"
                                                    class="inline-flex items-center justify-center p-2.5 text-emerald-600 hover:bg-emerald-50 rounded-2xl transition-all duration-200 group border border-transparent hover:border-emerald-100 shadow-sm hover:shadow-emerald-500/5"
                                                    title="Verify Service">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </span>

                                        <a href="{{ route('admin.services.show', $service->id) }}" 
                                            class="inline-flex items-center justify-center p-2.5 text-indigo-600 hover:bg-indigo-50 rounded-2xl transition-all duration-200 border border-transparent hover:border-indigo-100 shadow-sm hover:shadow-indigo-500/5"
                                            title="View Details">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 bg-slate-50 rounded-[2.5rem] flex items-center justify-center text-slate-200 mb-6 border border-slate-100 shadow-inner">
                                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-slate-900 text-lg font-black tracking-tight">No Market Services</p>
                                        <p class="text-slate-400 text-xs mt-2 font-bold uppercase tracking-widest opacity-60">Try adjusting your search or filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($services->hasPages())
                <div class="px-8 py-6 bg-slate-50/50 border-t border-gray-50">
                     {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function verifyService(button, serviceId) {
            if (!confirm('Are you sure you want to verify this service?')) return;

            const url = button.dataset.url;
            
            button.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
            button.disabled = true;

            axios.patch(url, {
                _token: '{{ csrf_token() }}'
            })
                .then(response => {
                    if (response.status === 200) {
                         window.location.reload(); 
                    }
                })
                .catch(error => {
                    console.error('Error verifying service:', error);
                    alert('Failed to verify service.');
                    window.location.reload();
                });
        }
    </script>
    @endpush
</x-admin-layout>
