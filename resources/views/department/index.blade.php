<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Departments</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 px-4">
        @if(session('success'))
            <div class="mb-4">
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m1 8a9 9 0 11-18 0 9 9 0 0118 0z" />
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

        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">All departments</h3>
                <p class="text-sm text-gray-500">Manage company departments</p>
            </div>
            <div>
                <a href="{{ route('department.create') }}" class="inline-block px-3 py-1 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">New Department</a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($departments as $department)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $department->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $department->employees_count ?? $department->users_count ?? $department->employees->count() ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ optional($department->created_at)->format('Y-m-d') ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-right space-x-2">
                                    <a href="{{ route('department.edit', $department) }}"
                                       class="inline-block px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                                        Edit
                                    </a>
                                    <form action="{{ route('department.destroy', $department) }}" method="POST" class="inline-block m-0 align-middle" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-block px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No departments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 bg-white border-t flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $departments->firstItem() ?? 0 }} to {{ $departments->lastItem() ?? 0 }} of {{ $departments->total() ?? 0 }}
                </div>
                <div>
                    {{ $departments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>