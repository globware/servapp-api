<x-admin-layout>
    @section('header', 'Services')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Service / User
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category/Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Requests
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Verified
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($services as $service)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-indigo-50 rounded flex items-center justify-center text-indigo-500 font-bold text-lg">
                                        {{ substr($service->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $service->name ?? $service->getType() }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            By {{ $service->user->firstname ?? 'Unknown' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                     {{ class_basename($service->type) ?? 'Service' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $service->state->name ?? 'N/A' }}, {{ $service->country->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $service->requests_count ?? 0 }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $service->verified ? 'Yes' : 'No' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $service->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-center justify-center" id="verify-cell-{{ $service->id }}">
                                @if($service->verified)
                                    <span class="text-green-600">Verified</span>
                                @else
                                    <button 
                                        onclick="verifyService(this, {{ $service->id }})" 
                                        data-url="{{ route('admin.services.verify', $service->id) }}"
                                        class=" text-indigo-600 px-2 py-1 hover:text-indigo-900 bg-green-50 border border-green-600 cursor-pointer font-bold">
                                        Verify
                                    </button>
                                @endif

                                <a href="{{ route('admin.services.show', $service->id) }}" 
                                    class="text-indigo-600 px-2 py-1 hover:text-indigo-900 bg-green-50 rounded border border-green-600 cursor-pointer font-bold">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No services found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
             {{ $services->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        function verifyService(button, serviceId) {
            if (!confirm('Are you sure you want to verify this service?')) return;

            const url = button.dataset.url;
            const originalText = button.innerText;
            
            button.innerText = 'Verifying...';
            button.disabled = true;

            axios.patch(url)
                .then(response => {
                    if (response.data.success) {
                        const cell = document.getElementById(`verify-cell-${serviceId}`);
                        cell.innerHTML = '<span class="text-green-600">Verified</span>';
                    }
                })
                .catch(error => {
                    console.error('Error verifying service:', error);
                    alert('Failed to verify service.');
                    button.innerText = originalText;
                    button.disabled = false;
                });
        }
    </script>
    @endpush
</x-admin-layout>
