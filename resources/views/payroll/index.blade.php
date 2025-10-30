<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Payroll Management
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- Success / Error Messages --}}
        @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 text-red-800">{{ session('error') }}</div>
        @endif

        {{-- Header Buttons --}}
        <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
            <div class="flex flex-wrap gap-2 items-center">
                <select id="month" class="border rounded p-2">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ ($m == ($month ?? now()->month)) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
                <select id="year" class="border rounded p-2">
                    @foreach(range(date('Y')-2, date('Y')) as $y)
                        <option value="{{ $y }}" {{ ($y == ($year ?? now()->year)) ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
                <button id="goPayroll" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full sm:w-auto">
                    Get Payroll Data
                </button>
                <script>
                    document.getElementById('goPayroll').addEventListener('click', function() {
                        const month = document.getElementById('month').value;
                        const year = document.getElementById('year').value;
                        window.location.href = `/payroll/index/${month}/${year}`;
                    });
                </script>
            </div>

            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('payroll.generate', ['month' => $month ?? now()->month, 'year' => $year ?? now()->year]) }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Are you sure to generate payroll? All finalized payrolls will become draft!')"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full sm:w-auto">
                        Generate Payroll
                    </button>
                </form>
                <form method="GET" action="{{ route('payroll.export', ['month' => $month ?? now()->month, 'year' => $year ?? now()->year]) }}">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 w-full sm:w-auto">
                        Export PDF
                    </button>
                </form>
                <form method="POST" action="{{ route('payroll.finalizeAll', ['month' => $month ?? now()->month, 'year' => $year ?? now()->year]) }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Finalize all payrolls for this month? This action cannot be undone!')"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 w-full sm:w-auto">
                        Finalize All
                    </button>
                </form>
            </div>
        </div>

        {{-- Payroll Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Department
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Total Pay
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Paid
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payrolls as $payroll)
                            <tr x-data="{ showDetails: false }" class="hover:bg-gray-50 transition">
                                {{-- Employee --}}
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $payroll->employee->user->name }}

                                    {{-- Mobile hidden details --}}
                                    <div x-show="showDetails" x-transition class="mt-2 space-y-1 text-xs text-gray-700 sm:hidden">
                                        <div><strong>Department:</strong> {{ $payroll->department_name }}</div>
                                        <div><strong>Total Pay:</strong> Rp {{ number_format($payroll->total_pay, 0, ',', '.') }}</div>
                                        <div><strong>Status:</strong>
                                            @if($payroll->status == 'finalized')
                                                <span class="text-green-600 font-semibold">Finalized</span>
                                            @else
                                                <span class="text-gray-500 font-semibold">Draft</span>
                                            @endif
                                        </div>
                                        <div><strong>Paid:</strong>
                                            @if($payroll->payment_datetime)
                                                <span class="text-green-600 font-semibold">{{ $payroll->payment_datetime }}</span>
                                            @else
                                                <span class="text-gray-500 font-semibold">Unpaid</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Desktop only columns --}}
                                <td class="px-6 py-4 text-center text-gray-700 hidden sm:table-cell">{{ $payroll->department_name }}</td>
                                <td class="px-6 py-4 text-center text-gray-700 hidden sm:table-cell">Rp {{ number_format($payroll->total_pay, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center text-gray-700 hidden sm:table-cell">
                                    @if($payroll->status == 'finalized')
                                        <span class="text-green-600 font-semibold">Finalized</span>
                                    @else
                                        <span class="text-gray-500 font-semibold">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700 hidden sm:table-cell">
                                    @if($payroll->payment_datetime)
                                        <span class="text-green-600 font-semibold">{{ $payroll->payment_datetime }}</span>
                                    @else
                                        <span class="text-gray-500 font-semibold">Unpaid</span>
                                    @endif
                                </td>

                                {{-- Actions + Mobile toggle --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-2 items-center w-full">

                                        {{-- Desktop + Mobile actions --}}
                                        <div class="flex flex-col gap-2 items-center w-full">
                                            @if($payroll->status == 'draft')
                                                <form method="POST" action="{{ route('payroll.finalize', $payroll) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            onclick="return confirm('Finalize this payroll?')"
                                                            class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs w-20">
                                                        Finalize
                                                    </button>
                                                </form>
                                                <a href="{{ route('payroll.edit', $payroll->id) }}"
                                                   class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs w-20 text-center">
                                                    Edit
                                                </a>
                                            @endif
                                            @if($payroll->status == 'finalized' && !$payroll->payment_datetime)
                                                <form method="POST" action="{{ route('payroll.pay', $payroll) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs w-20">
                                                        Pay
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('payroll.exportOne', $payroll) }}"
                                               class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs w-20 text-center">
                                                Export
                                            </a>
                                        </div>

                                        {{-- Mobile toggle button at bottom --}}
                                        <button @click="showDetails = !showDetails" class="w-full sm:hidden px-2 py-1 bg-gray-700 text-white rounded text-xs hover:bg-gray-800">
                                            <span x-text="showDetails ? 'Hide Details' : 'Details'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No payroll records found.</td>
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
