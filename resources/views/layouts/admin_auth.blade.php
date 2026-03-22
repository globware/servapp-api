<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ServeApp') }} Admin - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen bg-white dark:bg-gray-900 h-screen overflow-hidden">
            <!-- Left Side: Image -->
            <div class="hidden lg:block lg:w-1/2 relative overflow-hidden h-full">
                <img src="{{ asset('images/auth/admin_login_bg.png') }}" 
                     alt="Admin Login Background" 
                     class="absolute inset-0 w-full h-full object-cover transform hover:scale-105 transition-transform duration-1000">
                <div class="absolute inset-0 bg-indigo-900/20 mix-blend-multiply"></div>
                <!-- Overlay text -->
                <div class="absolute inset-0 flex flex-col justify-end p-16 bg-linear-to-t from-gray-900/80 via-transparent to-transparent">
                    <h2 class="text-white text-5xl font-extrabold mb-4 tracking-tight">Focus on Efficiency.</h2>
                    <p class="text-indigo-100 text-xl font-medium max-w-lg leading-relaxed">
                        Control your operations with the most powerful administration suite designed for ServeApp.
                    </p>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="w-full lg:w-1/2 flex flex-col items-center p-8 sm:p-16 bg-gray-50 dark:bg-gray-900 h-full overflow-y-auto">
                <div class="w-full max-w-md my-auto space-y-8">
                    <div class="text-center">
                        <a href="/" class="inline-block transition-transform hover:scale-105 duration-300">
                            <img src="{{ asset('logo.jpeg') }}" alt="ServeApp Logo" class="h-20 w-auto mx-auto drop-shadow-md">
                        </a>
                        <h1 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Admin Portal</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Welcome back! Please enter your credentials.
                        </p>
                    </div>

                    <div class="mt-8 bg-white dark:bg-gray-800 px-10 py-12 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-indigo-500/10">
                        {{ $slot }}
                    </div>
                    
                    <div class="text-center space-y-4">
                        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest font-semibold">
                            &copy; {{ date('Y') }} {{ config('app.name', 'ServeApp') }} &bull; Engineered Solutions
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
