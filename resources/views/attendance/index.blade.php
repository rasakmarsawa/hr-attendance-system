<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Attendance
        </h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Admin Buttons --}}
        @if(auth()->user()->role->name === 'Admin')
            <div class="flex flex-wrap gap-2 mb-6">
                <form action="{{ route('attendance.store') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full sm:w-auto">
                        Pre-fill Today Attendance
                    </button>
                </form>

                <a href="{{ route('attendance.report') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 w-full sm:w-auto text-center">
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
                            {{-- Key Column --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee
                            </th>

                            {{-- Hidden on mobile --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Department
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Check In
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Check Out
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Status
                            </th>

                            {{-- Mobile Only Toggle Button --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sm:hidden">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($attendances as $attendance)
                            @php
                                $status = ucfirst($attendance->status);
                                $rowColor = match($status) {
                                    'Late' => 'bg-orange-200',
                                    'Absent' => 'bg-red-200',
                                    default => 'bg-green-200',
                                };
                            @endphp

                            <tr x-data="{ showDetails: false }" class="{{ $rowColor }} hover:opacity-90 transition">
                                {{-- Employee (Key Column) --}}
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-normal break-words max-w-[200px]">
                                    {{ $attendance->user->name }}

                                    {{-- Hidden details in mobile --}}
                                    <div x-show="showDetails" x-transition class="mt-2 text-xs text-gray-700 space-y-1 sm:hidden">
                                        <div><strong>Department:</strong> {{ $attendance->user->employee->department->name ?? '-' }}</div>
                                        <div><strong>Date:</strong> {{ $attendance->date->format('d M Y') }}</div>
                                        <div><strong>Check In:</strong> {{ $attendance->check_in ?? '-' }}</div>
                                        <div><strong>Check Out:</strong> {{ $attendance->check_out ?? '-' }}</div>
                                        <div><strong>Status:</strong> {{ $status }}</div>
                                    </div>
                                </td>

                                {{-- Desktop Columns --}}
                                <td class="px-6 py-4 text-sm text-gray-800 hidden sm:table-cell">
                                    {{ $attendance->user->employee->department->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 hidden sm:table-cell">
                                    {{ $attendance->date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 hidden sm:table-cell">
                                    {{ $attendance->check_in ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 hidden sm:table-cell">
                                    {{ $attendance->check_out ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 font-semibold hidden sm:table-cell">
                                    {{ $status }}
                                </td>

                                {{-- Mobile Only Toggle --}}
                                <td class="px-6 py-4 text-center sm:hidden">
                                    <button @click="showDetails = !showDetails"
                                            class="w-full px-3 py-1 bg-gray-700 text-white rounded-md text-xs hover:bg-gray-800">
                                        <span x-text="showDetails ? 'Hide' : 'Details'"></span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
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
