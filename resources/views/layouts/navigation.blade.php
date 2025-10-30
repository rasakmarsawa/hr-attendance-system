<aside class="flex flex-col w-64 h-full bg-white border-r">
    <div class="px-6 py-4 border-b">
        <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-800">
            {{ config('app.name', 'Laravel') }}
        </a>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'block px-4 py-2 rounded-md text-sm text-indigo-700 bg-indigo-50 font-medium' : 'block px-4 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100' }}"
           aria-current="{{ request()->routeIs('dashboard') ? 'page' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('payroll.myPayroll') }}"
           class="{{ request()->is('my-payroll*') ? 'block px-4 py-2 rounded-md text-sm text-indigo-700 bg-indigo-50 font-medium' : 'block px-4 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100' }}"
           aria-current="{{ request()->is('my-payroll*') ? 'page' : '' }}">
            My Payroll
        </a>

        <a href="{{ route('user.edit', Auth::user()->id) }}"
           class="{{ request()->is('my-profile*') ? 'block px-4 py-2 rounded-md text-sm text-indigo-700 bg-indigo-50 font-medium' : 'block px-4 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100' }}"
           aria-current="{{ request()->is('my-profile*') ? 'page' : '' }}">
            Edit My Profile
        </a>

        @if(Auth::user() && Auth::user()->isAdmin())
            <a href="{{ route('user.index') }}"
               class="{{ request()->is('user*') ? 'block px-4 py-2 rounded-md text-sm text-indigo-700 bg-indigo-50 font-medium' : 'block px-4 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100' }}"
               aria-current="{{ request()->is('user*') ? 'page' : '' }}">
                User Management
            </a>

            <a href="{{ route('department.index') }}"
               class="{{ request()->is('department*') ? 'block px-4 py-2 rounded-md text-sm text-indigo-700 bg-indigo-50 font-medium' : 'block px-4 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100' }}"
               aria-current="{{ request()->is('department*') ? 'page' : '' }}">
                Department
            </a>

            <a href="{{ route('attendance.index') }}"
               class="{{ request()->is('attendance*') ? 'block px-4 py-2 rounded-md text-sm text-indigo-700 bg-indigo-50 font-medium' : 'block px-4 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100' }}"
               aria-current="{{ request()->is('attendance*') ? 'page' : '' }}">
                Attendance
            </a>

            @php
                $todayMonth = date('m');
                $todayYear  = date('Y');
            @endphp

            <a href="{{ route('payroll.index', ['month' => $todayMonth, 'year' => $todayYear]) }}"
               class="{{ request()->is('payroll*') ? 'block px-4 py-2 rounded-md text-sm text-indigo-700 bg-indigo-50 font-medium' : 'block px-4 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100' }}"
               aria-current="{{ request()->is('payroll*') ? 'page' : '' }}">
                Payroll
            </a>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="mt-4 px-4">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 rounded-md text-sm text-red-600 hover:bg-red-50">
                Logout
            </button>
        </form>
    </nav>
</aside>
