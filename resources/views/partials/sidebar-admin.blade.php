{{-- Admin Sidebar Navigation --}}

<a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">analytics</span>
    <span class="nav-text text-sm">Dashboard</span>
</a>

{{-- User Management Submenu --}}
<div>
    <button data-submenu="users-menu" class="nav-link flex items-center justify-between w-full px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.students*') || request()->routeIs('admin.staff*') ? 'active' : 'text-gray-400' }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined nav-icon" style="font-size:20px;">group</span>
            <span class="nav-text text-sm whitespace-nowrap">User Management</span>
        </div>
        <span class="material-symbols-outlined submenu-arrow text-xs transition-transform duration-200 {{ request()->routeIs('admin.students*') || request()->routeIs('admin.staff*') ? 'rotate-180' : '' }}" style="font-size:16px;">expand_more</span>
    </button>
    <div id="users-menu" class="submenu pl-10 space-y-0.5 {{ request()->routeIs('admin.students*') || request()->routeIs('admin.staff*') ? 'open' : '' }}">
        <a href="{{ route('admin.students') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('admin.students*') ? 'text-[#1392EC] font-medium' : '' }}">
            Students
        </a>
        <a href="{{ route('admin.staff') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('admin.staff*') ? 'text-[#1392EC] font-medium' : '' }}">
            Staff
        </a>
    </div>
</div>

{{-- Clinic Management Submenu --}}
<div>
    <button data-submenu="clinic-mgmt-menu" class="nav-link flex items-center justify-between w-full px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.services*') || request()->routeIs('admin.appointments*') ? 'active' : 'text-gray-400' }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined nav-icon" style="font-size:20px;">local_hospital</span>
            <span class="nav-text text-sm whitespace-nowrap">Clinic Management</span>
        </div>
        <span class="material-symbols-outlined submenu-arrow text-xs transition-transform duration-200 {{ request()->routeIs('admin.services*') || request()->routeIs('admin.appointments*') ? 'rotate-180' : '' }}" style="font-size:16px;">expand_more</span>
    </button>
    <div id="clinic-mgmt-menu" class="submenu pl-10 space-y-0.5 {{ request()->routeIs('admin.services*') || request()->routeIs('admin.appointments*') ? 'open' : '' }}">
        <a href="{{ route('admin.appointments') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('admin.appointments*') ? 'text-[#1392EC] font-medium' : '' }}">
            Appointments
        </a>
        <a href="{{ route('admin.services') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('admin.services*') ? 'text-[#1392EC] font-medium' : '' }}">
            Services
        </a>
    </div>
</div>

{{-- Certificate Management Submenu --}}
<div>
    <button data-submenu="cert-mgmt-menu" class="nav-link flex items-center justify-between w-full px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.certificate-types*') || request()->routeIs('admin.doctor-signatures*') ? 'active' : 'text-gray-400' }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined nav-icon" style="font-size:20px;">verified</span>
            <span class="nav-text text-sm whitespace-nowrap">Certificate Management</span>
        </div>
        <span class="material-symbols-outlined submenu-arrow text-xs transition-transform duration-200 {{ request()->routeIs('admin.certificate-types*') || request()->routeIs('admin.doctor-signatures*') ? 'rotate-180' : '' }}" style="font-size:16px;">expand_more</span>
    </button>
    <div id="cert-mgmt-menu" class="submenu pl-10 space-y-0.5 {{ request()->routeIs('admin.certificate-types*') || request()->routeIs('admin.doctor-signatures*') ? 'open' : '' }}">
        <a href="{{ route('admin.certificate-types') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('admin.certificate-types*') ? 'text-[#1392EC] font-medium' : '' }}">
            Certificate Types
        </a>
        <a href="{{ route('admin.doctor-signatures') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('admin.doctor-signatures*') ? 'text-[#1392EC] font-medium' : '' }}">
            Doctor Signatures
        </a>
    </div>
</div>

<a href="{{ route('admin.reports') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.reports') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">bar_chart</span>
    <span class="nav-text text-sm whitespace-nowrap">Reports & Analytics</span>
</a>

<a href="{{ route('admin.announcements') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.announcements*') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">campaign</span>
    <span class="nav-text text-sm">Announcements</span>
</a>

<div class="border-t border-white/5 my-3"></div>

<form action="/logout" method="POST">
    @csrf
    <button type="submit" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-red-400 w-full text-left">
        <span class="material-symbols-outlined nav-icon" style="font-size:20px;">logout</span>
        <span class="nav-text text-sm">Sign Out</span>
    </button>
</form>
