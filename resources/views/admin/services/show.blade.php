<x-admin-layout>
    @section('header')
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-xs font-black tracking-widest uppercase">
                <li><a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-indigo-600 transition-colors">Dashboard</a></li>
                <li class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <a href="{{ route('admin.services.index') }}" class="text-slate-400 hover:text-indigo-600 transition-colors">Market Services</a>
                </li>
                <li class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-slate-600">Service Blueprint</span>
                </li>
            </ol>
        </nav>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ $service->name ?? $service->getType() }}
            </h1>
            <div class="flex items-center space-x-3">
                 @if(!$service->verified)
                    <button onclick="verifyService(this, '{{ $service->id }}')" 
                        class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-black rounded-2xl hover:bg-emerald-700 shadow-sm transition-all duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Verify Service
                    </button>
                @else
                    <span class="px-5 py-2.5 bg-emerald-50 text-emerald-600 border border-emerald-100 text-sm font-black rounded-2xl flex items-center uppercase tracking-widest">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        Verified
                    </span>
                @endif
                <button class="p-2.5 text-slate-400 border border-gray-200 rounded-2xl hover:bg-slate-50 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
            </div>
        </div>
    @endsection

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Media Gallery -->
            <div class="bg-white rounded-4xl shadow-sm border border-gray-50 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em]">Service Portfolio</h3>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-600 border border-indigo-100">
                        {{ count($service->media ?? []) }} Assets
                    </span>
                </div>
                <div class="p-8">
                    @if(count($service->media ?? []) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($service->media as $media)
                                <div class="relative aspect-square group cursor-pointer overflow-hidden rounded-3xl border border-gray-100 bg-slate-50 transition-all duration-300 hover:border-indigo-200"
                                     onclick="openMediaModal('{{ $media->url }}', '{{ $media->type }}')">
                                    @if($media->type == 'image')
                                        <img src="{{ $media->url }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Service media">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 group-hover:text-indigo-500">
                                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="text-[10px] font-black uppercase tracking-widest">Preview Video</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-indigo-600/0 group-hover:bg-indigo-600/5 transition-colors duration-300 flex items-center justify-center">
                                        <div class="bg-white/95 backdrop-blur-sm p-3 rounded-2xl opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 shadow-xl">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 flex flex-col items-center justify-center text-slate-300 italic">
                            <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-bold tracking-widest uppercase">No media assets provided</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Service Details -->
            <div class="bg-white rounded-4xl shadow-sm border border-gray-50 p-8 space-y-8">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-6 flex items-center">
                         Description
                    </h3>
                    <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed font-medium">
                        {!! nl2br(e($service->description ?? 'No description available for this service.')) !!}
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-6">Service Archetypes & Meta</h3>
                    <div class="flex flex-wrap gap-2">
                        @if($service->tags && count($service->tags) > 0)
                            @foreach($service->tags as $tag)
                                <span class="px-4 py-2 rounded-2xl bg-slate-50 text-slate-600 text-xs font-bold border border-slate-100 hover:bg-slate-100 transition-colors">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        @else
                           <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">General Service</span>
                        @endif
                    </div>
                </div>
            </div>

             <!-- Complaints & Feedback -->
             <div class="bg-white rounded-4xl shadow-sm border border-gray-50 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em]">Compliance & Issues</h3>
                    <span class="px-3 py-1 bg-rose-50 text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-rose-100">
                         {{ $service->complaints_count ?? 0 }} Reports
                    </span>
                </div>
                
                <div class="space-y-4">
                    @forelse($service->complaints ?? [] as $complaint)
                        <div class="p-5 rounded-3xl bg-slate-50 border border-slate-100">
                             <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black text-slate-900">{{ $complaint->user->fullname ?? 'Anonymous' }}</span>
                                <span class="text-[10px] text-slate-400 font-bold">{{ $complaint->created_at->diffForHumans() }}</span>
                             </div>
                             <p class="text-xs text-slate-600 leading-relaxed font-medium">{{ $complaint->reason }}</p>
                        </div>
                    @empty
                        <div class="py-6 flex flex-col items-center justify-center text-slate-300">
                            <svg class="w-10 h-10 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">No compliance issues reported</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <!-- Provider Stats -->
            <div class="bg-indigo-600 rounded-4xl p-8 text-white shadow-lg shadow-indigo-200">
                <div class="flex items-center mb-6">
                    <div class="shrink-0 h-14 w-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white border border-white/20">
                         <span class="text-xl font-black">{{ substr($service->user->firstname ?? 'U', 0, 1) }}</span>
                    </div>
                    <div class="ml-4">
                        <div class="text-[10px] font-black uppercase tracking-widest opacity-70">Technician</div>
                        <div class="text-lg font-black leading-tight">{{ $service->user->firstname ?? 'Unknown' }} {{ $service->user->lastname ?? 'User' }}</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-4 border border-white/10">
                         <div class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Engaged</div>
                         <div class="text-2xl font-black">{{ $service->requests_count ?? 0 }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-4 border border-white/10">
                         <div class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Rating</div>
                         <div class="text-2xl font-black">4.8</div>
                    </div>
                </div>
            </div>

            <!-- Regional Details -->
            <div class="bg-white rounded-4xl shadow-sm border border-gray-50 p-8">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-6">Regionality</h3>
                <div class="space-y-5">
                    <div class="flex items-start">
                        <div class="shrink-0 h-10 w-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 border border-indigo-100">
                             <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Zone of Operation</span>
                            <span class="text-sm font-bold text-slate-800">{{ $service->state->name ?? 'N/A' }}, {{ $service->country->name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="shrink-0 h-10 w-10 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 border border-amber-100">
                             <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Availability</span>
                            <span class="text-sm font-bold text-slate-800">Mon - Fri, 08:00 - 18:00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Grid -->
            <div class="bg-white rounded-4xl shadow-sm border border-gray-50 p-8">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-6">Financial Matrix</h3>
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200/50">
                         <span class="text-xs font-black text-slate-500 uppercase tracking-wider">Starting Rate</span>
                         <span class="text-xl font-black text-indigo-600">N{{ number_format($service->price ?? 0, 2) }}</span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                            <span>Service Fee</span>
                            <span class="text-slate-900">10%</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                            <span>Admin Tax</span>
                            <span class="text-slate-900">5%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Modal -->
    <div id="mediaModal" class="fixed inset-0 z-100 hidden items-center justify-center p-4 bg-slate-900/95 backdrop-blur-md">
        <button onclick="closeMediaModal()" class="absolute top-6 right-6 p-3 text-white/50 hover:text-white transition-colors duration-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div class="max-w-5xl w-full aspect-video rounded-4xl overflow-hidden shadow-2xl ring-1 ring-white/10" id="modalContent"></div>
    </div>

    @push('scripts')
    <script>
        function openMediaModal(url, type) {
            const modal = document.getElementById('mediaModal');
            const content = document.getElementById('modalContent');
            
            if (type === 'image') {
                content.innerHTML = `<img src="${url}" class="w-full h-full object-contain bg-black/40" alt="Large preview">`;
            } else {
                content.innerHTML = `<video src="${url}" class="w-full h-full" controls autoplay></video>`;
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeMediaModal() {
            const modal = document.getElementById('mediaModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            document.getElementById('modalContent').innerHTML = '';
        }

        function verifyService(button, serviceId) {
            if (!confirm('Are you sure you want to verify this service?')) return;

            const url = "{{ route('admin.services.verify', $service->id) }}";
            
            button.innerHTML = `<svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24">...</svg> Processing...`;
            button.disabled = true;

            axios.patch(url, { _token: '{{ csrf_token() }}' })
                .then(response => { if (response.status === 200) window.location.reload(); })
                .catch(error => { alert('Verification failed.'); window.location.reload(); });
        }
    </script>
    @endpush
</x-admin-layout>