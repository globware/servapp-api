<x-admin-layout>
    @section('header', 'Service Details')

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <!-- Service Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.services.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="text-xl font-semibold text-gray-800">{{ $service->name }}</h2>
                @if(!$service->approved)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending Approval</span>
                @endif
                @if($service->verified)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Verified</span>
                @endif
                @if($service->suspended)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Suspended</span>
                @endif
            </div>
            
            @if(!$service->approved)
                <button onclick="approveService('{{ $service->id }}')" 
                        data-url="{{ route('admin.services.approve', $service->id) }}"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Approve Service
                </button>
            @endif
        </div>

        <!-- Service Content -->
        <div class="p-6">
            <!-- Service Media Gallery - Compact Version -->
            @if($service->media->count() > 0)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-900">Media Gallery</h3>
                        <span class="text-sm text-gray-500">{{ $service->media->count() }} {{ Str::plural('item', $service->media->count()) }}</span>
                    </div>
                    
                    <!-- Scrollable thumbnail strip -->
                    <div class="relative">
                        <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            @foreach($service->media as $media)
                                @php
                                    $extension = pathinfo($media->filename, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $mediaType = $isImage ? 'image' : 'document';
                                @endphp
                                
                                <div class="flex-shrink-0">
                                    <div class="relative group cursor-pointer" onclick="openMediaModal('{{ Storage::url($media->filename) }}', '{{ $mediaType }}')">
                                        @if($isImage)
                                            <div class="w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-indigo-500 transition-all duration-200">
                                                <img src="{{ Storage::url($media->path) }}" 
                                                     alt="{{ $media->alt_text ?? $service->name }}"
                                                     class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-20 h-20 rounded-lg bg-gray-100 border-2 border-gray-200 hover:border-indigo-500 transition-all duration-200 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Hover overlay -->
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-lg transition-all duration-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Scroll indicators (optional) -->
                        @if($service->media->count() > 6)
                            <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-white to-transparent pointer-events-none"></div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Service Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Basic Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Service Type</dt>
                            <dd class="text-sm text-gray-900">{{ class_basename($service->type) ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Provider</dt>
                            <dd class="text-sm text-gray-900">{{ $service->user->firstname ?? 'Unknown' }} {{ $service->user->lastname ?? '' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900">{{ $service->email ?? $service->user->email ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Phone Numbers</dt>
                            <dd class="text-sm text-gray-900">
                                @if($service->phone_numbers)
                                    @foreach($service->phone_numbers as $phone)
                                        <div>{{ $phone }}</div>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Location Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Location</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Country</dt>
                            <dd class="text-sm text-gray-900">{{ $service->country->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">State</dt>
                            <dd class="text-sm text-gray-900">{{ $service->state->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="text-sm text-gray-900">{{ $service->location->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="text-sm text-gray-900">{{ $service->address ?? 'N/A' }}</dd>
                        </div>
                        @if($service->latitude && $service->longitude)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">GPS Coordinates</dt>
                                <dd class="text-sm text-gray-900">{{ $service->latitude }}, {{ $service->longitude }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Operating Hours -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Operating Hours</h3>
                    <dl class="space-y-3">
                        @if($service->all_day)
                            <div class="text-sm text-gray-900">Open 24/7</div>
                        @else
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Opening Time</dt>
                                <dd class="text-sm text-gray-900">{{ $service->opening_time ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Closing Time</dt>
                                <dd class="text-sm text-gray-900">{{ $service->closing_time ?? 'N/A' }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Pricing -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Price Range</dt>
                            <dd class="text-sm text-gray-900">
                                @if($service->min_price && $service->max_price)
                                    ${{ number_format($service->min_price, 2) }} - ${{ number_format($service->max_price, 2) }}
                                @elseif($service->min_price)
                                    From ${{ number_format($service->min_price, 2) }}
                                @elseif($service->max_price)
                                    Up to ${{ number_format($service->max_price, 2) }}
                                @else
                                    Not specified
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Ratings</dt>
                            <dd class="text-sm text-gray-900">
                                @if($service->ratings)
                                    {{ number_format($service->ratings, 1) }} / 5
                                @else
                                    No ratings yet
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Description -->
            @if($service->description)
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700">
                        {{ $service->description }}
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if($service->tags->count() > 0)
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($service->tags as $tag)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tabs for Complaints and Tickets -->
            <div class="mt-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button onclick="switchTab('complaints')" 
                                id="tab-complaints"
                                class="tab-link border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Complaints
                        </button>
                        <button onclick="switchTab('tickets')" 
                                id="tab-tickets"
                                class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Tickets
                        </button>
                    </nav>
                </div>

                <!-- Complaints Tab Content -->
                <div id="content-complaints" class="tab-content mt-6">
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        @forelse($service->complaints ?? [] as $complaint)
                            <div class="border-b border-gray-200 last:border-0 p-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $complaint->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $complaint->description }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Reported by: {{ $complaint->reporter->name ?? 'Unknown' }}</p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($complaint->status === 'resolved') bg-green-100 text-green-800
                                        @elseif($complaint->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($complaint->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No complaints found</p>
                        @endforelse
                    </div>
                </div>

                <!-- Tickets Tab Content -->
                <div id="content-tickets" class="tab-content hidden mt-6">
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        @forelse($service->tickets ?? [] as $ticket)
                            <div class="border-b border-gray-200 last:border-0 p-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</p>
                                        <p class="text-sm text-gray-500">{{ $ticket->message }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Created: {{ $ticket->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($ticket->status === 'closed') bg-green-100 text-green-800
                                        @elseif($ticket->status === 'open') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No tickets found</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Modal -->
    <div id="mediaModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" onclick="closeMediaModal()">
        <div class="max-w-4xl max-h-screen p-4" onclick="event.stopPropagation()">
            <div class="relative">
                <button onclick="closeMediaModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div id="modalContent" class="flex items-center justify-center">
                    <!-- Content will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabName) {
            // Update tab links
            document.querySelectorAll('.tab-link').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById(`tab-${tabName}`).classList.add('border-indigo-500', 'text-indigo-600');

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
        }

        function approveService(serviceId) {
            if (!confirm('Are you sure you want to approve this service?')) return;

            const button = event.target;
            const url = button.dataset.url;
            const originalText = button.innerText;
            
            button.innerText = 'Approving...';
            button.disabled = true;

            axios.post(url)
                .then(response => {
                    if (response.data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error approving service:', error);
                    alert('Failed to approve service.');
                    button.innerText = originalText;
                    button.disabled = false;
                });
        }

        function openMediaModal(src, type) {
            const modal = document.getElementById('mediaModal');
            const modalContent = document.getElementById('modalContent');
            
            if (type === 'image') {
                modalContent.innerHTML = `<img src="${src}" class="max-w-full max-h-screen object-contain rounded-lg">`;
            } else {
                modalContent.innerHTML = `
                    <div class="bg-white p-8 rounded-lg text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-600">Preview not available for this file type</p>
                        <a href="${src}" target="_blank" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Download File
                        </a>
                    </div>
                `;
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeMediaModal() {
            const modal = document.getElementById('mediaModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('modalContent').innerHTML = '';
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMediaModal();
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        /* Custom scrollbar for the media gallery */
        .scrollbar-thin::-webkit-scrollbar {
            height: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>
    @endpush
</x-admin-layout>