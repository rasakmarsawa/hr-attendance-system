<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Payroll Management
        </h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        {{-- Success / Error Messages --}}
        @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif                    

        {{-- Payroll Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Employee column (desktop only) --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Employee
                            </th>

                            {{-- Desktop: other columns --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Department
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Total Pay
                            </th>

                            {{-- Period column is key column (always visible) --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Period
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Payment
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payrolls as $payroll)
                            <tr x-data="{ showDetails: false }" class="hover:bg-gray-50">
                                {{-- Employee (desktop only) --}}
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-normal break-words max-w-[200px] hidden sm:table-cell">
                                    {{ $payroll->employee->user->name }}
                                </td>

                                {{-- Department (desktop only) --}}
                                <td class="px-6 py-4 text-center text-sm text-gray-500 hidden sm:table-cell">
                                    {{ $payroll->employee->department->name }}
                                </td>

                                {{-- Total Pay (desktop only) --}}
                                <td class="px-6 py-4 text-center text-sm text-gray-500 hidden sm:table-cell">
                                    Rp {{ number_format($payroll->total_pay, 0, ',', '.') }}
                                </td>

                                {{-- Period column (key column, always visible) --}}
                                <td class="px-6 py-4 text-center text-sm text-gray-500 font-semibold">
                                    {{ \Carbon\Carbon::createFromDate(null, (int)$payroll->month, 1)->format('F') }} {{ $payroll->year }}

                                    {{-- Mobile: hidden details under period when toggled --}}
                                    <div x-show="showDetails" x-transition class="mt-2 text-xs text-gray-500 space-y-1 sm:hidden">
                                        <div><strong>Employee:</strong> {{ $payroll->employee->user->name }}</div>
                                        <div><strong>Department:</strong> {{ $payroll->employee->department->name }}</div>
                                        <div><strong>Total Pay:</strong> Rp {{ number_format($payroll->total_pay, 0, ',', '.') }}</div>
                                        <div><strong>Payment:</strong>
                                            @if($payroll->payment_datetime)
                                                <span class="text-green-600 font-semibold">{{ $payroll->payment_datetime }}</span>
                                            @else
                                                <span class="text-gray-500 font-semibold">Unpaid</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Payment (desktop only) --}}
                                <td class="px-6 py-4 text-center text-sm text-gray-500 hidden sm:table-cell">
                                    @if($payroll->payment_datetime)
                                        <span class="text-green-600 font-semibold">{{ $payroll->payment_datetime }}</span>
                                    @else
                                        <span class="text-gray-500 font-semibold">Unpaid</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex flex-col sm:flex-row sm:justify-center sm:items-center gap-2">
                                        @if($payroll->status === 'finalized')
                                            <a href="{{ route('payroll.exportOne', $payroll) }}"
                                               class="w-full sm:w-auto px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-xs text-center">
                                                Export
                                            </a>
                                        @endif

                                        {{-- Toggle button for mobile --}}
                                        <button @click="showDetails = !showDetails"
                                                class="w-full sm:w-auto px-2 py-1 bg-gray-200 text-gray-700 rounded-lg text-xs hover:bg-gray-300 sm:hidden">
                                            <span x-text="showDetails ? 'Hide Details' : 'Show Details'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No payroll records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($payrolls->hasPages())
            <div class="mt-6">
                {{ $payrolls->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
