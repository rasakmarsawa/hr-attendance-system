<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Payroll</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8 px-4">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('payroll.update', $payroll) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Employee Info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Name</label>
                        <p class="mt-1 text-gray-900">{{ $payroll->employee->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                        <p class="mt-1 text-gray-900">EMP-{{ $payroll->employee->user_id }}</p>
                    </div>
                </div> 
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Period</label>
                        <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::createFromDate(null, (int)$payroll->month, 1)->format('F') }} {{ $payroll->year }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Daily Rate</label>
                        <p class="mt-1 text-gray-900">Rp {{ number_format($payroll->employee->daily_rate, 0, ',', '.') }}</p>
                    </div>
                </div>                  

                
                    

                {{-- Total Present --}}
                <div>
                    <label for="total_present" class="block text-sm font-medium text-gray-700">Days Present</label>
                    <div class="mt-1">
                        <input
                            type="number"
                            name="total_present"
                            id="total_present"
                            min="0"
                            value="{{ old('total_present', $payroll->total_present) }}"
                            required
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>
                    @error('total_present')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Absent --}}
                <div>
                    <label for="total_absent" class="block text-sm font-medium text-gray-700">Days Absent</label>
                    <div class="mt-1">
                        <input
                            type="number"
                            name="total_absent"
                            id="total_absent"
                            min="0"
                            value="{{ old('total_absent', $payroll->total_absent) }}"
                            required
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>
                    @error('total_absent')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Late --}}
                <div>
                    <label for="total_late" class="block text-sm font-medium text-gray-700">Days Late</label>
                    <div class="mt-1">
                        <input
                            type="number"
                            name="total_late"
                            id="total_late"
                            min="0"
                            value="{{ old('total_late', $payroll->total_late) }}"
                            required
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>
                    @error('total_late')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Daily Rate --}}
                <div>
                    <label for="daily_rate" class="block text-sm font-medium text-gray-700">Daily Rate (Rp)</label>
                    <div class="mt-1">
                        <input
                            type="number"
                            name="daily_rate"
                            id="daily_rate"
                            min="0"
                            value="{{ old('daily_rate', $payroll->daily_rate) }}"
                            required
                            class="block w-60 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>
                    @error('daily_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Pay --}}
                <div>
                    <label for="total_pay" class="block text-sm font-medium text-gray-700">Total Pay (Rp)</label>
                    <div class="mt-1">
                        <input
                            type="number"
                            name="total_pay"
                            id="total_pay"
                            min="0"
                            value="{{ old('total_pay', $payroll->total_pay) }}"
                            required
                            class="block w-60 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>
                    @error('total_pay')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('payroll.index', [$payroll->month, $payroll->year]) }}"
                        class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        Update Payroll
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
