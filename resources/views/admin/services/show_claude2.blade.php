<x-admin-layout>
    @section('header', 'Service Details')

    <!-- Page Header with Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 transition-colors">Dashboard</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('admin.services.index') }}" class="hover:text-gray-700 transition-colors">Services</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-900 font-medium">{{ Str::limit($service->name, 30) }}</span>
        </nav>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.services.index') }}" 
                   class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 group">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $service->name }}</h1>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-sm text-gray-500">ID: {{ $service->id }}</span>
                        <span class="text-gray-300">•</span>
                        <span class="text-sm text-gray-500">Created {{ $service->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                @if(!$service->approved)
                    <button onclick="approveService('{{ $service->id }}')" 
                            data-url="{{ route('admin.services.approve', $service->id) }}"
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm hover:shadow transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Service
                    </button>
                @endif
                
                <!-- Action Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 z-10"
                         style="display: none;">
                        <div class="py-1">
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Service
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Public Page
                            </a>
                        </div>
                        <div class="py-1">
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Badges Bar -->
    @if(!$service->approved || $service->verified || $service->suspended)
        <div class="flex items-center space-x-2 mb-6">
            @if(!$service->approved)
                <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200">
                    <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-amber-800">Pending Approval</span>
                </div>
            @endif
            @if($service->verified)
                <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200">
                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-emerald-800">Verified</span>
                </div>
            @endif
            @if($service->suspended)
                <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 border border-red-200">
                    <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                    <span class="text-sm font-medium text-red-800">Suspended</span>
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content Column -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Media Gallery -->
            @if($service->media->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Media Gallery</h3>
                                <p class="text-sm text-gray-500">{{ $service->media->count() }} {{ Str::plural('file', $service->media->count()) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <div class="flex items-center space-x-1.5">
                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                <span>{{ $service->media->filter(fn($f) => str_starts_with($f->mime_type, 'image/'))->count() }}</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                                <span>{{ $service->media->filter(fn($f) => str_starts_with($f->mime_type, 'video/'))->count() }}</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                                <span>{{ $service->media->filter(fn($f) => !str_starts_with($f->mime_type, 'image/') && !str_starts_with($f->mime_type, 'video/'))->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Thumbnail Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($service->media as $index => $file)
                            @php
                                $isImage = str_starts_with($file->mime_type, 'image/');
                                $isVideo = str_starts_with($file->mime_type, 'video/');
                                $fileUrl = Storage::disk($file->disk)->url($file->path);
                            @endphp
                            
                            <div class="group relative aspect-square cursor-pointer overflow-hidden rounded-xl border-2 border-gray-200 hover:border-indigo-500 transition-all duration-300 hover:shadow-lg"
                                onclick="openMediaModal('{{ $fileUrl }}', '{{ $file->mime_type }}', '{{ addslashes($file->original_filename) }}', '{{ $index }}')">
                                
                                @if($isImage)
                                    <!-- Image Thumbnail -->
                                    <img src="{{ $fileUrl }}" 
                                        alt="{{ $file->original_filename }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                    
                                    <!-- Image Overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="absolute bottom-0 left-0 right-0 p-3">
                                            <div class="flex items-center justify-between text-white">
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span class="text-xs font-medium">Image</span>
                                                </div>
                                                <span class="text-xs font-medium">{{ $file->formatted_size }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Zoom Icon -->
                                    <div class="absolute top-2 right-2 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 shadow-lg">
                                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                    </div>
                                    
                                @elseif($isVideo)
                                    <!-- Video Thumbnail -->
                                    <div class="w-full h-full bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600 flex flex-col items-center justify-center relative">
                                        <div class="relative">
                                            <!-- Play Button Circle -->
                                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-purple-600 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Video Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <div class="absolute bottom-0 left-0 right-0 p-3">
                                                <div class="flex items-center justify-between text-white">
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                                        </svg>
                                                        <span class="text-xs font-medium">Video</span>
                                                    </div>
                                                    <span class="text-xs font-medium">{{ $file->formatted_size }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Video Badge -->
                                        <div class="absolute top-2 left-2 px-2 py-1 bg-black/50 backdrop-blur-sm rounded text-white text-xs font-medium">
                                            VIDEO
                                        </div>
                                    </div>
                                    
                                @else
                                    <!-- Document Thumbnail -->
                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex flex-col items-center justify-center relative group-hover:from-gray-200 group-hover:to-gray-300 transition-colors duration-300">
                                        @php
                                            $iconData = match($file->extension) {
                                                'pdf' => ['icon' => '📄', 'color' => 'from-red-500 to-red-600', 'name' => 'PDF'],
                                                'doc', 'docx' => ['icon' => '📝', 'color' => 'from-blue-500 to-blue-600', 'name' => 'DOC'],
                                                'xls', 'xlsx' => ['icon' => '📊', 'color' => 'from-green-500 to-green-600', 'name' => 'XLS'],
                                                'zip', 'rar', '7z' => ['icon' => '📦', 'color' => 'from-yellow-500 to-yellow-600', 'name' => 'ZIP'],
                                                'txt' => ['icon' => '📃', 'color' => 'from-gray-500 to-gray-600', 'name' => 'TXT'],
                                                'ppt', 'pptx' => ['icon' => '📽️', 'color' => 'from-orange-500 to-orange-600', 'name' => 'PPT'],
                                                default => ['icon' => '📁', 'color' => 'from-gray-500 to-gray-600', 'name' => 'FILE']
                                            };
                                        @endphp
                                        
                                        <div class="text-5xl mb-2 transform group-hover:scale-110 transition-transform duration-300">
                                            {{ $iconData['icon'] }}
                                        </div>
                                        <div class="px-3 py-1 bg-gradient-to-r {{ $iconData['color'] }} text-white text-xs font-bold rounded-full">
                                            {{ $iconData['name'] }}
                                        </div>
                                        
                                        <!-- Document Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <div class="absolute bottom-0 left-0 right-0 p-3">
                                                <div class="flex items-center justify-between text-white">
                                                    <span class="text-xs font-medium truncate max-w-[120px]">{{ $file->extension }}</span>
                                                    <span class="text-xs font-medium">{{ $file->formatted_size }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Download Icon -->
                                        <div class="absolute top-2 right-2 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 shadow-lg">
                                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Media Type Badge -->
                                <div class="absolute top-2 left-2 w-3 h-3 rounded-full border-2 border-white shadow-sm
                                    @if($isImage) bg-blue-500
                                    @elseif($isVideo) bg-purple-500
                                    @else bg-gray-500
                                    @endif">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($service->media->count() > 12)
                        <div class="mt-6 text-center">
                            <button onclick="toggleGalleryExpand()" 
                                    id="galleryExpandBtn"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                <span id="expandBtnText">Show All Media</span>
                                <svg id="expandBtnIcon" class="ml-2 w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Description -->
            @if($service->description)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $service->description }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if($service->tags->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Tags</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2">
                            @foreach($service->tags as $tag)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-sm font-medium text-blue-900 hover:from-blue-100 hover:to-indigo-100 transition-colors duration-200">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Complaints and Tickets Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-1 px-6" aria-label="Tabs">
                        <button onclick="switchTab('complaints')" 
                                id="tab-complaints"
                                class="tab-link group relative px-6 py-4 font-medium text-sm transition-all duration-200 border-b-2 border-indigo-500 text-indigo-600">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>Complaints</span>
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                    {{ $service->complaints_count ?? 0 }}
                                </span>
                            </div>
                        </button>
                        <button onclick="switchTab('tickets')" 
                                id="tab-tickets"
                                class="tab-link group relative px-6 py-4 font-medium text-sm transition-all duration-200 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                                <span>Tickets</span>
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                    {{ $service->tickets_count ?? 0 }}
                                </span>
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- Complaints Tab Content -->
                <div id="content-complaints" class="tab-content">
                    <div class="divide-y divide-gray-100">
                        @forelse($service->complaints ?? [] as $complaint)
                            <div class="p-6 hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $complaint->title }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($complaint->status === 'resolved') bg-emerald-100 text-emerald-800
                                                @elseif($complaint->status === 'pending') bg-amber-100 text-amber-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($complaint->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">{{ $complaint->description }}</p>
                                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span>{{ $complaint->reporter->name ?? 'Unknown' }}</span>
                                            </div>
                                            <span class="text-gray-300">•</span>
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ $complaint->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">No complaints found</p>
                                <p class="text-sm text-gray-400 mt-1">This service has a clean record</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tickets Tab Content -->
                <div id="content-tickets" class="tab-content hidden">
                    <div class="divide-y divide-gray-100">
                        @forelse($service->tickets ?? [] as $ticket)
                            <div class="p-6 hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="text-base font-semibold text-gray-900">{{ $ticket->subject }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($ticket->status === 'closed') bg-emerald-100 text-emerald-800
                                                @elseif($ticket->status === 'open') bg-blue-100 text-blue-800
                                                @else bg-amber-100 text-amber-800 @endif">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">{{ $ticket->message }}</p>
                                        <div class="flex items-center space-x-1 text-xs text-gray-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>Created {{ $ticket->created_at->format('M d, Y \a\t H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">No tickets found</p>
                                <p class="text-sm text-gray-400 mt-1">No support tickets have been created</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="space-y-6">
            
            <!-- Quick Stats -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-sm overflow-hidden text-white">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Quick Stats
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <span class="text-sm text-white/90">Ratings</span>
                            <span class="text-lg font-bold">
                                @if($service->ratings)
                                    {{ number_format($service->ratings, 1) }}/5
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <span class="text-sm text-white/90">Media Files</span>
                            <span class="text-lg font-bold">{{ $service->media->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                            <span class="text-sm text-white/90">Tags</span>
                            <span class="text-lg font-bold">{{ $service->tags->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Basic Information
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Service Type</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ class_basename($service->type) ?? 'N/A' }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Provider</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $service->user->firstname ?? 'Unknown' }} {{ $service->user->lastname ?? '' }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email</dt>
                            <dd class="text-sm text-gray-900">
                                <a href="mailto:{{ $service->email ?? $service->user->email ?? '' }}" class="text-indigo-600 hover:text-indigo-700 hover:underline">
                                    {{ $service->email ?? $service->user->email ?? 'N/A' }}
                                </a>
                            </dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Phone Numbers</dt>
                            <dd class="text-sm text-gray-900">
                                @if($service->phone_numbers)
                                    @foreach($service->phone_numbers as $phone)
                                        <div class="flex items-center space-x-2 mb-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            <span>{{ $phone }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Location
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Country</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $service->country->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">State</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $service->state->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Location</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $service->location->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Address</dt>
                            <dd class="text-sm text-gray-900">{{ $service->address ?? 'N/A' }}</dd>
                        </div>
                        @if($service->latitude && $service->longitude)
                            <div class="border-t border-gray-100 pt-4">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">GPS Coordinates</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $service->latitude }}, {{ $service->longitude }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Operating Hours -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Operating Hours
                    </h3>
                </div>
                <div class="p-6">
                    @if($service->all_day)
                        <div class="flex items-center justify-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-semibold text-green-900">Open 24/7</span>
                        </div>
                    @else
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Opening Time</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $service->opening_time ?? 'N/A' }}</dd>
                            </div>
                            <div class="border-t border-gray-100 pt-4">
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Closing Time</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $service->closing_time ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pricing
                    </h3>
                </div>
                <div class="p-6">
                    <div class="text-center p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Price Range</div>
                        <div class="text-2xl font-bold text-gray-900">
                            @if($service->min_price && $service->max_price)
                                ${{ number_format($service->min_price, 2) }} - ${{ number_format($service->max_price, 2) }}
                            @elseif($service->min_price)
                                From ${{ number_format($service->min_price, 2) }}
                            @elseif($service->max_price)
                                Up to ${{ number_format($service->max_price, 2) }}
                            @else
                                <span class="text-base text-gray-500">Not specified</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Modal -->
    <div id="mediaModal" 
        class="fixed inset-0 bg-black/90 backdrop-blur-sm hidden items-center justify-center z-50 p-4" 
        onclick="closeMediaModal()">
        <div class="max-w-7xl w-full h-full flex flex-col" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 bg-white/10 backdrop-blur-md rounded-t-2xl">
                <div class="flex items-center space-x-4 flex-1 min-w-0">
                    <button onclick="navigateMedia(-1)" 
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors duration-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <div class="flex-1 min-w-0">
                        <h3 id="modalFileName" class="text-white font-semibold truncate text-lg"></h3>
                        <p id="modalFileInfo" class="text-white/70 text-sm"></p>
                    </div>
                    
                    <button onclick="navigateMedia(1)" 
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors duration-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="flex items-center space-x-2 ml-4">
                    <button id="modalDownloadBtn" 
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors duration-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </button>
                    
                    <button onclick="closeMediaModal()" 
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors duration-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 flex items-center justify-center bg-black/50 backdrop-blur-sm rounded-b-2xl overflow-hidden">
                <div id="modalContent" class="w-full h-full flex items-center justify-center p-8">
                    <!-- Content will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    @php
    $mediaFiles = $service->media->map(function($file) {
        return [
            'url' => Storage::disk($file->disk)->url($file->path),
            'mime_type' => $file->mime_type,
            'filename' => $file->original_filename,
            'size' => $file->formatted_size,
            'extension' => $file->extension
        ];
    })->values();
@endphp

<!-- Hidden data container -->
<script type="application/json" id="mediaFilesData">
    {!! json_encode($mediaFiles) !!}
</script>

    @push('scripts')
    <script>
        let mediaFiles = JSON.parse(document.getElementById('mediaFilesData').textContent);
        let currentMediaIndex = 0;

        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab-link').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            const activeTab = document.getElementById(`tab-${tabName}`);
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600');

            // Update content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
        }

        function approveService(serviceId) {
            if (!confirm('Are you sure you want to approve this service?')) return;

            const button = event.target;
            const url = button.dataset.url;
            const originalHtml = button.innerHTML;
            
            button.innerHTML = `
                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Approving...
            `;
            button.disabled = true;

            axios.post(url)
                .then(response => {
                    if (response.data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error approving service:', error);
                    alert('Failed to approve service. Please try again.');
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                });
        }

        function openMediaModal(url, mimeType, filename, index) {
        currentMediaIndex = index;
        displayMedia();
        
        const modal = document.getElementById('mediaModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function displayMedia() {
        const media = mediaFiles[currentMediaIndex];
        const modalContent = document.getElementById('modalContent');
        const modalFileName = document.getElementById('modalFileName');
        const modalFileInfo = document.getElementById('modalFileInfo');
        const modalDownloadBtn = document.getElementById('modalDownloadBtn');
        
        modalFileName.textContent = media.filename;
        modalFileInfo.textContent = `${media.size} • ${currentMediaIndex + 1} of ${mediaFiles.length}`;
        
        // Update download button
        modalDownloadBtn.onclick = () => {
            const link = document.createElement('a');
            link.href = media.url;
            link.download = media.filename;
            link.click();
        };
        
        modalContent.innerHTML = '';
        
        if (media.mime_type.startsWith('image/')) {
            modalContent.innerHTML = `
                <img src="${media.url}" 
                     alt="${media.filename}" 
                     class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
            `;
        } 
        else if (media.mime_type.startsWith('video/')) {
            modalContent.innerHTML = `
                <video controls autoplay class="max-w-full max-h-full rounded-lg shadow-2xl">
                    <source src="${media.url}" type="${media.mime_type}">
                    Your browser does not support the video tag.
                </video>
            `;
        }
        else if (media.mime_type === 'application/pdf') {
            modalContent.innerHTML = `
                <iframe src="${media.url}" class="w-full h-full rounded-lg shadow-2xl bg-white"></iframe>
            `;
        } 
        else {
            const iconMap = {
                'pdf': '📄',
                'doc': '📝', 'docx': '📝',
                'xls': '📊', 'xlsx': '📊',
                'zip': '📦', 'rar': '📦', '7z': '📦',
                'txt': '📃',
                'ppt': '📽️', 'pptx': '📽️'
            };
            
            const icon = iconMap[media.extension] || '📁';
            
            modalContent.innerHTML = `
                <div class="text-center">
                    <div class="text-8xl mb-6">${icon}</div>
                    <h4 class="text-white text-xl font-semibold mb-2">Preview Not Available</h4>
                    <p class="text-white/70 mb-6">This file type cannot be previewed</p>
                    <a href="${media.url}" 
                       download 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download File
                    </a>
                </div>
            `;
        }
    }

    function navigateMedia(direction) {
        currentMediaIndex += direction;
        
        if (currentMediaIndex < 0) {
            currentMediaIndex = mediaFiles.length - 1;
        } else if (currentMediaIndex >= mediaFiles.length) {
            currentMediaIndex = 0;
        }
        
        displayMedia();
    }

    function closeMediaModal() {
        const modal = document.getElementById('mediaModal');
        const modalContent = document.getElementById('modalContent');
        const video = modalContent.querySelector('video');
        
        if (video) {
            video.pause();
        }
        
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modalContent.innerHTML = '';
        document.body.style.overflow = '';
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

        

        // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('mediaModal');
        if (!modal.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeMediaModal();
            } else if (e.key === 'ArrowLeft') {
                navigateMedia(-1);
            } else if (e.key === 'ArrowRight') {
                navigateMedia(1);
            }
        }
    });

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
    </script>
    @endpush

    @push('styles')
    <style>
        /* Smooth transitions for gallery */
    .group:hover img {
        transform: scale(1.1);
    }
    
    /* Modal animations */
    #mediaModal {
        animation: fadeIn 0.2s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    /* Custom scrollbar for modal */
    #modalContent::-webkit-scrollbar {
        width: 8px;
    }
    
    #modalContent::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }
    
    #modalContent::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }
    
    #modalContent::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }


        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Smooth transitions */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Modal animations */
        
        #mediaModal.flex {
            opacity: 1;
        }
        
        #mediaModal.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* Video player focus */
        video:focus {
            outline: none;
        }

        /* Custom focus styles */
        button:focus, a:focus {
            outline: 2px solid rgb(99, 102, 241);
            outline-offset: 2px;
        }

        /* Improved hover effects */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(to right, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
    @endpush
</x-admin-layout>