<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance Detail — {{ $user->name }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">

        {{-- Header Info --}}
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-700">
                    {{ \Carbon\Carbon::create()->month((int)$month)->format('F') }} {{ $year }}
                </h3>
                <p class="text-sm text-gray-500">
                    Department: {{ $user->employee->department->name ?? '-' }}
                </p>
            </div>
            <a href="{{ route('attendance.report', ['month' => $month, 'year' => $year]) }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                ← Back
            </a>
        </div>

        {{-- Attendance Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Check In</th>
                        <th class="px-4 py-2 text-center">Check Out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('d F Y') }}
                            </td>
                            <td class="px-4 py-2 text-center">
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
                            <td class="px-4 py-2 text-center">
                                {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                No attendance records for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
