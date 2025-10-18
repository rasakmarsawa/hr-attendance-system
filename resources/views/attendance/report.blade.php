<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Monthly Attendance Report
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        {{-- Filter Form --}}
        <form method="GET" class="mb-4 flex gap-2">
            <select name="month" class="border rounded p-2">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>

            <select name="year" class="border rounded p-2">
                @foreach(range(date('Y') - 2, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Filter
            </button>

            <a href="{{ route('attendance.export', ['month' => $month, 'year' => $year]) }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center">
                Export to Excel
            </a>            
        </form>

        {{-- Attendance Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Employee</th>
                        <th class="px-4 py-2 text-center">Present</th>
                        <th class="px-4 py-2 text-center">Absent</th>
                        <th class="px-4 py-2 text-center">Late</th>
                        <th class="px-4 py-2 text-center">Total Days</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report as $row)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $row['name'] }}</td>
                            <td class="px-4 py-2 text-center">{{ $row['present'] }}</td>
                            <td class="px-4 py-2 text-center">{{ $row['absent'] }}</td>
                            <td class="px-4 py-2 text-center">{{ $row['late'] }}</td>
                            <td class="px-4 py-2 text-center">{{ $row['total_days'] }}</td>
                            <td class="px-4 py-2 text-center">
                                <a href="{{ route('attendance.detail', ['user_id' => $row['user_id'], 'month' => $month, 'year' => $year]) }}"
                                   class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-xs">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                No attendance data found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
    