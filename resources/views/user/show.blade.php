<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            User Details
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8 px-4">

        {{-- Success message --}}
        @if(session('success'))
            <div class="mb-4">
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m1 8a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- USER INFO CARD --}}
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Left column --}}
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Name</h4>
                        <p class="text-base text-gray-900">{{ $user->name }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Email</h4>
                        <p class="text-base text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Role</h4>
                        <p class="text-base text-gray-900">{{ ucfirst($user->role->name) }}</p>
                    </div>
                </div>

                {{-- Right column --}}
                <div class="space-y-4 sm:text-right">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Created At</h4>
                        <p class="text-base text-gray-900">
                            {{ $user->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Updated At</h4>
                        <p class="text-base text-gray-900">
                            {{ $user->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('user.index') }}"
                    class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                    Back
                </a>

                <div class="space-x-2">
                    @if(auth()->user()->role->name === 'Admin')
                        <a href="{{ route('user.edit', $user) }}"
                            class="inline-block px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                            Edit
                        </a>
                        <form action="{{ route('user.destroy', $user) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- HR DATA CARD --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">HR Data</h3>

            @if(!$user->employee)
                {{-- No HR data yet --}}
                <div class="text-gray-600 mb-4">
                    HR data not set yet.
                </div>
                <a href="{{ route('employee.create', $user) }}"
                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create HR Data
                </a>
            @else
                {{-- HR data exists --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Employee Code</h4>
                        <p class="text-base text-gray-900">EMP-{{ $user->id }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Department</h4>
                        <p class="text-base text-gray-900">{{ $user->employee->department->name ?? '-' }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Position</h4>
                        <p class="text-base text-gray-900">{{ $user->employee->position }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Daily Rate</h4>
                        <p class="text-base text-gray-900">Rp {{ number_format($user->employee->daily_rate, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Joining Date</h4>
                        <p class="text-base text-gray-900">
                            {{ \Carbon\Carbon::parse($user->employee->joining_date)->format('d M Y') }}
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Status</h4>
                        <p class="text-base text-gray-900">{{ ucfirst($user->employee->status) }}</p>
                    </div>
                </div>

                {{-- HR Action buttons --}}
                <div class="mt-6 flex items-center justify-end space-x-2">
                    <a href="{{ route('employee.edit', $user->employee->id) }}"
                        class="inline-block px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                        Edit
                    </a>
                    <form action="{{ route('employee.destroy', $user->employee->id) }}" method="POST"
                        onsubmit="return confirm('Delete this HR data?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
