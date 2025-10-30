<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" x-data="{ openNav: false }">
    <div class="min-h-screen bg-gray-100 flex">

        <!-- Sidebar (mobile + desktop) -->
        <div 
            class="fixed inset-y-0 left-0 w-64 bg-white border-r shadow-md transform transition-transform duration-200 ease-in-out z-50"
            :class="{ '-translate-x-full': !openNav, 'translate-x-0': openNav, 'lg:translate-x-0': true }"
        >
            @include('layouts.navigation')
        </div>

        <!-- Main content -->
        <div class="flex-1 lg:pl-64 flex flex-col">
            
            <!-- Top bar (mobile only) -->
            <div class="bg-white shadow flex justify-between items-center px-4 py-3 lg:hidden">
                <div class="text-lg font-semibold text-gray-800">{{ config('app.name', 'Laravel') }}</div>
                <button @click="openNav = !openNav" class="text-gray-600 focus:outline-none">
                    <!-- Hamburger Icon -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Header (desktop only) -->
            @isset($header)
                <header class="bg-white shadow hidden lg:block">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 px-4">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Overlay for mobile -->
        <div 
            class="fixed inset-0 bg-black bg-opacity-40 lg:hidden z-40"
            x-show="openNav"
            @click="openNav = false"
            x-transition.opacity
        ></div>
    </div>
</body>
</html>
