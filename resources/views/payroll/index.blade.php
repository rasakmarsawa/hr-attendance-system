<form method="GET" action="{{ route('payroll.monthly') }}" class="flex gap-2 items-center">
    <select name="month" class="border rounded p-2">
        @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $m == date('m') ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
            </option>
        @endforeach
    </select>
    <select name="year" class="border rounded p-2">
        @foreach(range(date('Y')-2, date('Y')) as $y)
            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        See Monthly Payroll
    </button>
</form>
