<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit HR Data</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8 px-4">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('employee.update', $employee->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Hidden user_id --}}
                <input type="hidden" name="user_id" value="{{ $employee->user_id }}">

                {{-- Department --}}
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                    <div class="mt-1">
                        <select
                            name="department_id"
                            id="department_id"
                            required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="">-- Select Department --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Position --}}
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                    <div class="mt-1">
                        <input
                            type="text"
                            name="position"
                            id="position"
                            value="{{ old('position', $employee->position) }}"
                            required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Daily Rate --}}
                <div>
                    <label for="daily_rate" class="block text-sm font-medium text-gray-700">Daily Rate</label>
                    <div class="mt-1">
                        <input
                            type="number"
                            name="daily_rate"
                            id="daily_rate"
                            value="{{ old('daily_rate', $employee->daily_rate) }}"
                            required
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>
                    @error('daily_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Join Date --}}
                <div>
                    <label for="join_date" class="block text-sm font-medium text-gray-700">Join Date</label>
                    <div class="mt-1">
                        <input
                            type="date"
                            name="join_date"
                            id="join_date"
                            value="{{ old('join_date', $employee->join_date ? $employee->join_date->format('Y-m-d') : '') }}"
                            required
                            class="block w-60 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                    </div>
                    @error('join_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <div class="mt-1">
                        <select
                            name="status"
                            id="status"
                            required
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="">-- Select Status --</option>
                            <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('user.show', $employee->user_id) }}"
                        class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        Update HR Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
