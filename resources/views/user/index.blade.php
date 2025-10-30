<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            User
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

        {{-- Header --}}
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">All users</h3>
                <p class="text-sm text-gray-500">Manage application users</p>
            </div>
            <div>
                <a href="{{ route('user.create') }}" class="inline-block px-3 py-1 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                    New User
                </a>
            </div>
        </div>

        {{-- User Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Name column: key --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>

                            {{-- Other columns (desktop only) --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Status
                            </th>

                            {{-- Actions --}}
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr x-data="{ showDetails: false }" class="hover:bg-gray-50">
                                {{-- Name (key column) --}}
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-normal break-words max-w-[200px]">
                                    {{ $user->name }}

                                    {{-- Mobile hidden details --}}
                                    <div x-show="showDetails" x-transition class="mt-2 text-xs text-gray-500 space-y-1 sm:hidden">
                                        <div><strong>Email:</strong> {{ $user->email }}</div>
                                        <div><strong>Role:</strong> {{ $user->role->name }}</div>
                                        <div><strong>Status:</strong> {{ $user->employee->status ?? '-' }}</div>
                                    </div>
                                </td>

                                {{-- Desktop columns --}}
                                <td class="px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">{{ $user->role->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">{{ $user->employee->status ?? '-' }}</td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center gap-2">
                                        <a href="{{ route('user.show', $user) }}"
                                           class="w-full sm:w-auto px-3 py-1 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 text-xs text-center">
                                            Show
                                        </a>
                                        <a href="{{ route('user.edit', $user) }}"
                                           class="w-full sm:w-auto px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs text-center">
                                            Edit
                                        </a>
                                        <form action="{{ route('user.destroy', $user) }}" method="POST" class="w-full sm:w-auto inline-block m-0 align-middle" onsubmit="return confirm('Delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-full sm:w-auto px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs text-center">
                                                Delete
                                            </button>
                                        </form>

                                        {{-- Mobile toggle button --}}
                                        <button @click="showDetails = !showDetails"
                                                class="w-full sm:hidden px-2 py-1 bg-gray-200 text-gray-700 rounded-lg text-xs hover:bg-gray-300">
                                            <span x-text="showDetails ? 'Hide Details' : 'Show Details'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 bg-white border-t flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }}
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
