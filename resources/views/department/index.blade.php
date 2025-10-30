<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Departments</h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        {{-- Success Message --}}
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

        {{-- Header Section --}}
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-lg font-medium text-gray-900">All Departments</h3>
                <p class="text-sm text-gray-500">Manage company departments</p>
            </div>
            <div>
                <a href="{{ route('department.create') }}"
                   class="inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded-lg shadow hover:bg-blue-700 transition">
                    + New Department
                </a>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Employees
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Created
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($departments as $department)
                            <tr x-data="{ showDetails: false }" class="hover:bg-gray-50">
                                {{-- Name column --}}
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-normal break-words max-w-[200px]">
                                    {{ $department->name }}

                                    {{-- Mobile details under name --}}
                                    <div x-show="showDetails" x-transition class="mt-2 text-xs text-gray-500 space-y-1 sm:hidden">
                                        <div><strong>Employees:</strong> {{ $department->employees_count ?? $department->users_count ?? $department->employees->count() ?? '-' }}</div>
                                        <div><strong>Created:</strong> {{ optional($department->created_at)->format('Y-m-d') ?? '-' }}</div>
                                    </div>
                                </td>

                                {{-- Employees column (desktop only) --}}
                                <td class="px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">
                                    {{ $department->employees_count ?? $department->users_count ?? $department->employees->count() ?? '-' }}
                                </td>

                                {{-- Created column (desktop only) --}}
                                <td class="px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">
                                    {{ optional($department->created_at)->format('Y-m-d') ?? '-' }}
                                </td>

                                {{-- Actions column --}}
                                <td class="px-6 py-4 text-sm text-right">
                                    <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center gap-2">
                                        <a href="{{ route('department.edit', $department) }}"
                                           class="w-full sm:w-auto px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300 text-center">
                                            Edit
                                        </a>
                                        <form action="{{ route('department.destroy', $department) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this department?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-full sm:w-auto px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 text-center">
                                                Delete
                                            </button>
                                        </form>

                                        {{-- Row toggle button (mobile only) --}}
                                        <button @click="showDetails = !showDetails"
                                                class="w-full sm:w-auto px-2 py-1 bg-gray-200 text-gray-700 rounded-lg text-xs hover:bg-gray-300 sm:hidden">
                                            <span x-text="showDetails ? 'Hide Details' : 'Show Details'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No departments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 bg-white border-t flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="text-sm text-gray-700">
                    Showing {{ $departments->firstItem() ?? 0 }} to {{ $departments->lastItem() ?? 0 }}
                    of {{ $departments->total() ?? 0 }}
                </div>
                <div class="w-full sm:w-auto">
                    {{ $departments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
