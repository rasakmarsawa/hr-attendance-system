<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Attendance
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 px-4">

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

        @if(auth()->user()->role->name === 'Admin')
            <div class="flex space-x-2 mb-6">
                <form action="{{ route('attendance.store') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Pre-fill Today Attendance
                    </button>
                </form>

                <a href="{{ route('attendance.report') }}" 
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Monthly Report
                </a>
            </div>
        @endif



        {{-- Attendance Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($attendances as $attendance)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $attendance->user->employee->department->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $attendance->date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $attendance->check_in ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $attendance->check_out ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst($attendance->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No attendance records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
