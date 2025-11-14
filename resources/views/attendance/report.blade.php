<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Monthly Attendance Report
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- Filter Form --}}
        <form method="GET" class="mb-6 flex flex-wrap gap-2">
            <select name="month" class="border rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-400">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>

            <select name="year" class="border rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-400">
                @foreach(range(date('Y') - 2, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm w-full sm:w-auto">
                Filter
            </button>

            <a href="{{ route('attendance.export', ['month' => $month, 'year' => $year]) }}"
               class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm text-center w-full sm:w-auto">
                Export to Excel
            </a>
        </form>

        {{-- Attendance Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Key column --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee
                            </th>

                            {{-- Hidden in mobile --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Present
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Absent
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Late
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Total Days
                            </th>

                            {{-- Action column --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($report as $row)
                            <tr x-data="{ showDetails: false }" class="hover:bg-gray-50 transition">
                                {{-- Key Column --}}
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-normal break-words max-w-[200px]">
                                    {{ $row['name'] }}

                                    {{-- Mobile hidden details --}}
                                    <div x-show="showDetails" x-transition class="mt-2 text-xs text-gray-700 space-y-1 sm:hidden">
                                        <div><strong>Present:</strong> {{ $row['present'] }}</div>
                                        <div><strong>Absent:</strong> {{ $row['absent'] }}</div>
                                        <div><strong>Late:</strong> {{ $row['late'] }}</div>
                                        <div><strong>Total Days:</strong> {{ $row['total_days'] }}</div>
                                    </div>
                                </td>

                                {{-- Desktop-only columns --}}
                                <td class="px-6 py-4 text-center text-gray-800 hidden sm:table-cell">
                                    {{ $row['present'] }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-800 hidden sm:table-cell">
                                    {{ $row['absent'] }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-800 hidden sm:table-cell">
                                    {{ $row['late'] }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-800 hidden sm:table-cell">
                                    {{ $row['total_days'] }}
                                </td>

                                {{-- Action Column --}}
                                <td class="px-6 py-4 text-center space-y-2 sm:space-y-0 sm:space-x-2">
                                    {{-- Desktop: Daily Data --}}
                                    <a href="{{ route('attendance.detail', ['user_id' => $row['user_id'], 'month' => $month, 'year' => $year]) }}"
                                       class="hidden sm:inline-block bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-xs">
                                        Daily Data
                                    </a>

                                    {{-- Mobile: Daily Data + toggle button --}}
                                    <div class="flex flex-col gap-2 sm:hidden">
                                        <a href="{{ route('attendance.detail', ['user_id' => $row['user_id'], 'month' => $month, 'year' => $year]) }}"
                                           class="w-full bg-indigo-600 text-white px-3 py-1 rounded-md hover:bg-indigo-700 text-xs text-center">
                                            Daily Data
                                        </a>
                                        <button @click="showDetails = !showDetails"
                                                class="w-full px-3 py-1 bg-gray-700 text-white rounded-md text-xs hover:bg-gray-800">
                                            <span x-text="showDetails ? 'Hide Details' : 'Details'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No attendance data found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
