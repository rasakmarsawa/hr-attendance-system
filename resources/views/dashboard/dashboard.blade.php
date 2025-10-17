<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    {{-- Success message --}}
                    @if(session('success'))
                        <div class="mb-4">
                            <div class="rounded-md bg-green-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m1 8a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">
                                            {{ session('success') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Error / Fail message --}}
                    @if(session('error'))
                        <div class="mb-4">
                            <div class="rounded-md bg-red-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800">
                                            {{ session('error') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Welcome, {{ auth()->user()->name }}</h3>

                <p class="text-gray-700 mb-4">Manage your daily attendance below.</p>

                {{-- Attendance Status --}}
                @php
                    $today = \Carbon\Carbon::today()->toDateString();
                    $attendance = auth()->user()->attendances()->where('date', $today)->first();
                @endphp

                @if($attendance)
                    <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                        <h4 class="text-sm font-semibold text-gray-600 mb-2">Today's Attendance</h4>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Check In</p>
                                <p class="text-base text-gray-900">
                                    {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Check Out</p>
                                <p class="text-base text-gray-900">
                                    {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            @if(!$attendance->check_in)
                                <form action="{{ route('attendance.checkin') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                        Check In
                                    </button>
                                </form>
                            @elseif(!$attendance->check_out)
                                <form action="{{ route('attendance.checkout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                        Check Out
                                    </button>
                                </form>
                            @else
                                <p class="text-sm text-gray-600 italic">You’ve completed attendance today.</p>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- No attendance record for today --}}
                    <div class="text-gray-600 mb-4">
                        No attendance record found for today.
                    </div>
                    <form action="{{ route('attendance.checkin') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Check In for Today
                        </button>
                    </form>
                @endif
            </div>

            @if(auth()->user()->role->name === 'Admin')
                @include('dashboard.partials.admin')
            @else
                @include('dashboard.partials.employee')
            @endif
        </div>
    </div>
</x-app-layout>
