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
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex">
        @include('layouts.navigation')

            <!-- Main content column -->
            <div class="flex-1 lg:pl-64">
                <!-- Top navigation (mobile & small screens) -->
                <header class="bg-white border-b lg:hidden">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between h-16">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-gray-800">{{ config('app.name', 'Laravel') }}</a>
                            </div>
                            <div class="flex items-center">
                                <!-- simple links for mobile -->
                                <a href="#" class="text-sm text-gray-700 mr-4">Posts</a>
                                <a href="#" class="text-sm text-gray-700">Dashboard</a>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 px-4">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
