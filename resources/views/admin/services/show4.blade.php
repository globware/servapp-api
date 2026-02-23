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
            <!-- Service Media Gallery - Compact Version -->
@if($service->media->count() > 0)
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-medium text-gray-900">Media Gallery</h3>
            <div class="flex items-center space-x-3">
                <span class="text-xs text-gray-500">
                    <span class="inline-flex items-center mr-2"><span class="w-2 h-2 bg-blue-500 rounded-full mr-1"></span> {{ $service->media->filter(fn($f) => str_starts_with($f->mime_type, 'image/'))->count() }}</span>
                    <span class="inline-flex items-center mr-2"><span class="w-2 h-2 bg-purple-500 rounded-full mr-1"></span> {{ $service->media->filter(fn($f) => str_starts_with($f->mime_type, 'video/'))->count() }}</span>
                    <span class="inline-flex items-center"><span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span> {{ $service->media->filter(fn($f) => !str_starts_with($f->mime_type, 'image/') && !str_starts_with($f->mime_type, 'video/'))->count() }}</span>
                </span>
            </div>
        </div>
        
        <!-- Scrollable thumbnail strip -->
        <div class="relative">
            <div class="flex space-x-1.5 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                @foreach($service->media as $file)
                    @php
                        $isImage = str_starts_with($file->mime_type, 'image/');
                        $isVideo = str_starts_with($file->mime_type, 'video/');
                        $fileUrl = Storage::disk($file->disk)->url($file->path);
                    @endphp
                    
                    <div class="flex-shrink-0">
                        <div class="relative group cursor-pointer" onclick="openMediaModal('{{ $fileUrl }}', '{{ $file->mime_type }}', '{{ $file->original_filename }}')">
                            @if($isImage)
                                <div class="w-14 h-14 rounded-lg overflow-hidden border border-gray-200 hover:border-indigo-500 transition-all duration-200">
                                    <img src="{{ $fileUrl }}" 
                                         alt="{{ $file->original_filename }}"
                                         class="w-full h-full object-cover">
                                </div>
                            @elseif($isVideo)
                                <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 border border-gray-200 hover:border-purple-500 transition-all duration-200 flex items-center justify-center relative">
                                    <span class="text-xl">🎬</span>
                                    <!-- Small play icon overlay on hover -->
                                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg">
                                        <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 0a10 10 0 1 0 0 20 10 10 0 0 0 0-20zM8 14.5v-9l6 4.5-6 4.5z"/>
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <div class="w-14 h-14 rounded-lg bg-gray-100 border border-gray-200 hover:border-indigo-500 transition-all duration-200 flex items-center justify-center">
                                    @php
                                        $icon = match($file->extension) {
                                            'pdf' => '📄',
                                            'doc', 'docx' => '📝',
                                            'xls', 'xlsx' => '📊',
                                            'zip', 'rar', '7z' => '📦',
                                            default => '📁'
                                        };
                                    @endphp
                                    <span class="text-xl">{{ $icon }}</span>
                                </div>
                            @endif
                            
                            <!-- Media type indicator dot (optional - can be removed if too cluttered) -->
                            <div class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border border-white
                                @if($isImage) bg-blue-500
                                @elseif($isVideo) bg-purple-500
                                @else bg-gray-500
                                @endif">
                            </div>
                            
                            <!-- File size tooltip - appears on hover -->
                            <div class="absolute -bottom-7 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none z-10">
                                {{ $file->formatted_size }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Scroll indicators -->
            @if($service->media->count() > 8)
                <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white to-transparent pointer-events-none"></div>
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
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 whitespace-pre-line">
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
                            Complaints ({{ $service->complaints_count ?? 0 }})
                        </button>
                        <button onclick="switchTab('tickets')" 
                                id="tab-tickets"
                                class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Tickets ({{ $service->tickets_count ?? 0 }})
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
                                        <p class="text-xs text-gray-400 mt-1">Reported by: {{ $complaint->reporter->name ?? 'Unknown' }} • {{ $complaint->created_at->diffForHumans() }}</p>
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
        <div class="max-w-6xl max-h-screen p-4 w-full" onclick="event.stopPropagation()">
            <div class="relative bg-white rounded-lg overflow-hidden">
                <!-- Modal header -->
                <div class="flex items-center justify-between px-4 py-2 bg-gray-100 border-b border-gray-200">
                    <span id="modalFileName" class="text-sm font-medium text-gray-700 truncate max-w-md"></span>
                    <button onclick="closeMediaModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal content -->
                <div id="modalContent" class="flex items-center justify-center p-4 bg-gray-900 max-h-[80vh] overflow-auto">
                    <!-- Content will be inserted here -->
                </div>
                
                <!-- Modal footer for downloads -->
                <div id="modalFooter" class="hidden px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-end">
                    <!-- Footer content will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-link').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById(`tab-${tabName}`).classList.add('border-indigo-500', 'text-indigo-600');

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

        function openMediaModal(url, mimeType, filename) {
            const modal = document.getElementById('mediaModal');
            const modalContent = document.getElementById('modalContent');
            const modalFooter = document.getElementById('modalFooter');
            const modalFileName = document.getElementById('modalFileName');
            
            modalFileName.textContent = filename;
            modalContent.innerHTML = '';
            
            if (mimeType.startsWith('image/')) {
                modalContent.innerHTML = `<img src="${url}" alt="${filename}" class="max-w-full max-h-[70vh] object-contain">`;
                modalFooter.classList.add('hidden');
            } 
            else if (mimeType.startsWith('video/')) {
                modalContent.innerHTML = `
                    <video controls class="max-w-full max-h-[70vh]">
                        <source src="${url}" type="${mimeType}">
                        Your browser does not support the video tag.
                    </video>
                `;
                modalFooter.innerHTML = `
                    <a href="${url}" download class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Video
                    </a>
                `;
                modalFooter.classList.remove('hidden');
            }
            else if (mimeType === 'application/pdf') {
                modalContent.innerHTML = `<iframe src="${url}" class="w-full h-[70vh]"></iframe>`;
                modalFooter.innerHTML = `
                    <a href="${url}" download class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download PDF
                    </a>
                `;
                modalFooter.classList.remove('hidden');
            } 
            else {
                modalContent.innerHTML = `
                    <div class="text-center p-8">
                        <div class="text-6xl mb-4">
                            ${getFileIcon(mimeType, url.split('.').pop())}
                        </div>
                        <p class="text-white mb-4">This file type cannot be previewed</p>
                    </div>
                `;
                modalFooter.innerHTML = `
                    <a href="${url}" download class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download File
                    </a>
                `;
                modalFooter.classList.remove('hidden');
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function getFileIcon(mimeType, extension) {
            if (mimeType.startsWith('video/')) return '🎬';
            if (mimeType.startsWith('audio/')) return '🎵';
            
            const iconMap = {
                'pdf': '📄',
                'doc': '📝', 'docx': '📝',
                'xls': '📊', 'xlsx': '📊',
                'zip': '📦', 'rar': '📦', '7z': '📦',
                'txt': '📃',
                'ppt': '📽️', 'pptx': '📽️'
            };
            
            return iconMap[extension] || '📁';
        }

        function closeMediaModal() {
            const modal = document.getElementById('mediaModal');
            const modalContent = document.getElementById('modalContent');
            const video = modalContent.querySelector('video');
            
            // Stop video playback if it exists
            if (video) {
                video.pause();
            }
            
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalContent.innerHTML = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMediaModal();
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
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

        /* Video player styles */
        video:focus {
            outline: none;
        }
        
        /* Modal animations */
        #mediaModal {
            transition: opacity 0.2s ease-in-out;
        }
        
        #mediaModal.flex {
            opacity: 1;
        }
        
        #mediaModal.hidden {
            opacity: 0;
            pointer-events: none;
        }
    </style>
    @endpush
</x-admin-layout>