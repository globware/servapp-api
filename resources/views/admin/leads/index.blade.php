<x-admin-layout>
    @section('header', 'Suggested Leads')

    <div class="space-y-6">
        
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-4 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Header Actions & Filters -->
        <div class="bg-white rounded-4xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.leads.index', ['status' => 'pending']) }}" 
                       class="px-4 py-2 rounded-xl {{ $status === 'pending' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:bg-slate-50' }}">
                        Pending Approvals
                    </a>
                    <a href="{{ route('admin.leads.index', ['status' => 'approved']) }}" 
                       class="px-4 py-2 rounded-xl {{ $status === 'approved' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:bg-slate-50' }}">
                        Approved Leads
                    </a>
                </div>
            </div>
        </div>

        <!-- Leads Table -->
        <div class="bg-white rounded-4xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Business Name</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type / Category</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Address / Location</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Submitted By</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                            @if($status === 'pending')
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($leads as $lead)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $lead->business_name ?? $lead->title }}</div>
                                    <div class="text-sm text-slate-500 max-w-xs truncate">{{ $lead->description }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ ucfirst($lead->lead_type) }}
                                    </span>
                                    <div class="text-sm text-slate-500 mt-1">{{ $lead->category_text }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-700">{{ $lead->address }}</div>
                                    @if($lead->latitude)
                                        <div class="text-xs text-slate-400 mt-1">{{ $lead->latitude }}, {{ $lead->longitude }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    User ID: {{ $lead->suggested_by }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $lead->created_at->format('M j, Y') }}
                                </td>
                                @if($status === 'pending')
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.leads.approve', $lead->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition-colors">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.leads.reject', $lead->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject and delete this lead?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $status === 'pending' ? '6' : '5' }}" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                        <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900">No {{ $status }} leads found</h3>
                                    <p class="mt-1 text-sm text-slate-500">When users suggest businesses, they will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($leads->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $leads->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
