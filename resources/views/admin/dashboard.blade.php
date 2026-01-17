<x-admin-layout>
    @section('header', 'Dashboard')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Users Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Users</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="p-3 bg-indigo-50 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span class="font-medium">Active</span>
            </div>
        </div>

        <!-- Services Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Services</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalServices) }}</p>
                </div>
                <div class="p-3 bg-pink-50 rounded-full">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
             <div class="mt-4 flex items-center text-sm text-green-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span class="font-medium">Verified</span>
            </div>
        </div>

        <!-- Admins Card -->
         <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Admins</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalAdmins) }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
             <div class="mt-4 flex items-center text-sm text-gray-500">
                <span class="font-medium">Staff Members</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('admin.users.index') }}" class="block p-4 border border-gray-100 rounded-lg hover:shadow-md transition-shadow">
                <div class="font-medium text-indigo-600 mb-1">Manage Users</div>
                <p class="text-sm text-gray-500">View details and manage registered users.</p>
            </a>
            <a href="{{ route('admin.services.index') }}" class="block p-4 border border-gray-100 rounded-lg hover:shadow-md transition-shadow">
                <div class="font-medium text-pink-600 mb-1">Manage Services</div>
                <p class="text-sm text-gray-500">Review and oversee user-submitted services.</p>
            </a>
        </div>
    </div>
</x-admin-layout>
