<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Payroll Management
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 px-4">

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
        </div>


        {{-- Payroll Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pay</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payrolls as $payroll)
                            <tr>
                                <td class="px-6 py-4">{{ $payroll->employee->user->name }}</td>                                
                                <td class="px-6 py-4 text-center">{{ $payroll->employee->department->name }}</td>                
                                <td class="px-6 py-4 text-center">Rp {{ number_format($payroll->total_pay, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">{{ \Carbon\Carbon::createFromDate(null, (int)$payroll->month, 1)->format('F') }} {{ $payroll->year }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($payroll->payment_datetime)
                                        <span class="text-green-600 font-semibold">{{ $payroll->payment_datetime }}</span>
                                    @else
                                        <span class="text-gray-500 font-semibold">Unpaid</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($payroll->status === 'finalized')
                                    <div class="flex flex-col gap-2 items-center">
                                        <a href="{{ route('payroll.exportOne', $payroll) }}" 
                                           class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs w-20 text-center">
                                            Export
                                        </a>
                                    </div>
                                    @endif
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

        @if($payrolls->hasPages())
            <div class="mt-6">
                {{ $payrolls->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
