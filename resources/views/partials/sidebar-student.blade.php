{{-- Student Sidebar Navigation --}}

<a href="{{ route('student.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.dashboard') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">dashboard</span>
    <span class="nav-text text-sm">My Dashboard</span>
</a>

<a href="{{ route('student.health') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.health') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">favorite</span>
    <span class="nav-text text-sm">My Health</span>
</a>

<a href="{{ route('student.appointments') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.appointments') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">calendar_month</span>
    <span class="nav-text text-sm">My Appointments</span>
</a>

<a href="{{ route('student.visit-history') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.visit-history') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">history</span>
    <span class="nav-text text-sm">Visit History</span>
</a>

<a href="{{ route('student.services') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.services*') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">medical_services</span>
    <span class="nav-text text-sm">Services</span>
</a>

{{-- Certificates Submenu --}}
<div>
    <button data-submenu="certificates-menu" class="nav-link flex items-center justify-between w-full px-3 py-2.5 rounded-xl {{ request()->routeIs('student.certificates*') ? 'active' : 'text-gray-400' }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined nav-icon" style="font-size:20px;">description</span>
            <span class="nav-text text-sm">Certificates</span>
        </div>
        <span class="material-symbols-outlined submenu-arrow text-xs transition-transform duration-200 {{ request()->routeIs('student.certificates*') ? 'rotate-180' : '' }}" style="font-size:16px;">expand_more</span>
    </button>
    <div id="certificates-menu" class="submenu pl-10 space-y-0.5 {{ request()->routeIs('student.certificates*') ? 'open' : '' }}">
        <a href="{{ route('student.certificates.request') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('student.certificates.request*') ? 'text-[#1392EC] font-medium' : '' }}">
            Certificate Request
        </a>
        <a href="{{ route('student.certificates.my') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('student.certificates.my') ? 'text-[#1392EC] font-medium' : '' }}">
            My Certificates
        </a>
    </div>
</div>

<a href="{{ route('student.announcements') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.announcements') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">campaign</span>
    <span class="nav-text text-sm">Announcements</span>
</a>

<div class="border-t border-white/5 my-3"></div>

<a href="{{ route('student.profile') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.profile') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">person</span>
    <span class="nav-text text-sm">My Profile</span>
</a>

<form action="/logout" method="POST">
    @csrf
    <button type="submit" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-red-400 w-full text-left">
        <span class="material-symbols-outlined nav-icon" style="font-size:20px;">logout</span>
        <span class="nav-text text-sm">Sign Out</span>
    </button>
</form>
