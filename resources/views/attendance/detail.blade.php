<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance Detail — {{ $user->name }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- Header Info --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-700">
                    {{ \Carbon\Carbon::create()->month((int)$month)->format('F') }} {{ $year }}
                </h3>
                <p class="text-sm text-gray-500">
                    Department: {{ $user->employee->department->name ?? '-' }}
                </p>
            </div>

            <a href="{{ route('attendance.report', ['month' => $month, 'year' => $year]) }}"
               class="mt-3 sm:mt-0 inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                ← Back
            </a>
        </div>

        {{-- Attendance Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Key Column --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>

                            {{-- Status --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>

                            {{-- Hidden on mobile --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Check In
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Check Out
                            </th>

                            {{-- Mobile action column --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sm:hidden">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($attendances as $attendance)
                            <tr x-data="{ showDetails: false }" class="hover:bg-gray-50 transition">
                                {{-- Date --}}
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('d F Y') }}

                                    {{-- Mobile hidden detail section --}}
                                    <div x-show="showDetails" x-transition class="mt-2 text-xs text-gray-700 sm:hidden space-y-1">
                                        <div><strong>Check In:</strong>
                                            {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                                        </div>
                                        <div><strong>Check Out:</strong>
                                            {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">
                                    @if($attendance->status === 'present')
                                        <span class="text-green-600 font-semibold">Present</span>
                                    @elseif($attendance->status === 'absent')
                                        <span class="text-red-600 font-semibold">Absent</span>
                                    @elseif($attendance->status === 'late')
                                        <span class="text-yellow-600 font-semibold">Late</span>
                                    @else
                                        <span class="text-gray-500 font-semibold">{{ ucfirst($attendance->status) }}</span>
                                    @endif
                                </td>

                                {{-- Desktop only --}}
                                <td class="px-6 py-4 text-center text-gray-700 hidden sm:table-cell">
                                    {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700 hidden sm:table-cell">
                                    {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                                </td>

                                {{-- Mobile action --}}
                                <td class="px-6 py-4 text-center sm:hidden">
                                    <button @click="showDetails = !showDetails"
                                            class="w-full px-3 py-1 bg-gray-700 text-white rounded-md text-xs hover:bg-gray-800">
                                        <span x-text="showDetails ? 'Hide Details' : 'Details'"></span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No attendance records for this month.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
