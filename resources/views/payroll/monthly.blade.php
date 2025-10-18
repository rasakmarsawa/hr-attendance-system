{{-- Bulk Actions for this month/year --}}
<div class="flex gap-2 mb-4">
    <form method="POST" action="{{ route('payroll.generate') }}">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit" onclick="return confirm('Generate payroll for {{ $month }}/{{ $year }}? All finalized payrolls will become draft!')"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Generate Payroll
        </button>
    </form>

    <form method="POST" action="{{ route('payroll.finalizeAll') }}">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit" onclick="return confirm('Finalize ALL payrolls for {{ $month }}/{{ $year }}?')"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Finalize All
        </button>
    </form>

    <a href="{{ route('payroll.export', ['month' => $month, 'year' => $year]) }}"
       class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
       Export PDF
    </a>
</div>

{{-- Table of payrolls for selected month/year --}}
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th>Employee</th>
            <th>Present</th>
            <th>Absent</th>
            <th>Late</th>
            <th>Total Pay</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payrolls as $payroll)
            <tr>
                <td>{{ $payroll->employee->user->name }}</td>
                <td>{{ $payroll->total_present }}</td>
                <td>{{ $payroll->total_absent }}</td>
                <td>{{ $payroll->total_late }}</td>
                <td>Rp {{ number_format($payroll->total_pay,0,',','.') }}</td>
                <td>{{ $payroll->finalized ? 'Finalized' : 'Draft' }}</td>
                <td>
                    @if(!$payroll->finalized)
                        <form method="POST" action="{{ route('payroll.finalize', $payroll->id) }}">
                            @csrf
                            <button onclick="return confirm('Finalize this payroll?')">Finalize</button>
                        </form>
                    @endif
                    <a href="{{ route('payroll.show', $payroll->id) }}">View</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No payroll data for this month/year</td>
            </tr>
        @endforelse
    </tbody>
</table>
