{{-- Staff Sidebar Navigation --}}

<a href="{{ route('staff.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('staff.dashboard') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">dashboard</span>
    <span class="nav-text text-sm">Dashboard</span>
</a>

{{-- Clinical Submenu --}}
<div>
    <button data-submenu="clinical-menu" class="nav-link flex items-center justify-between w-full px-3 py-2.5 rounded-xl {{ request()->routeIs('staff.appointments*') || request()->routeIs('staff.record-visits') || request()->routeIs('staff.patients*') || request()->routeIs('staff.availability*') ? 'active' : 'text-gray-400' }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined nav-icon" style="font-size:20px;">clinical_notes</span>
            <span class="nav-text text-sm">Clinical</span>
        </div>
        <span class="material-symbols-outlined submenu-arrow text-xs transition-transform duration-200 {{ request()->routeIs('staff.appointments*') || request()->routeIs('staff.record-visits') || request()->routeIs('staff.patients*') || request()->routeIs('staff.availability*') ? 'rotate-180' : '' }}" style="font-size:16px;">expand_more</span>
    </button>
    <div id="clinical-menu" class="submenu pl-10 space-y-0.5 {{ request()->routeIs('staff.appointments*') || request()->routeIs('staff.record-visits') || request()->routeIs('staff.patients*') || request()->routeIs('staff.availability*') ? 'open' : '' }}">
        <a href="{{ route('staff.appointments') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('staff.appointments*') ? 'text-[#1392EC] font-medium' : '' }}">
            Appointment Requests
        </a>
        <a href="{{ route('staff.record-visits') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('staff.record-visits') ? 'text-[#1392EC] font-medium' : '' }}">
            Record Visits
        </a>
        <a href="{{ route('staff.patients') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('staff.patients*') ? 'text-[#1392EC] font-medium' : '' }}">
            Patients
        </a>
        <a href="{{ route('staff.availability') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('staff.availability*') ? 'text-[#1392EC] font-medium' : '' }}">
            My Availability
        </a>
    </div>
</div>

{{-- Workflow Submenu --}}
<div>
    <button data-submenu="workflow-menu" class="nav-link flex items-center justify-between w-full px-3 py-2.5 rounded-xl {{ request()->routeIs('staff.tasks*') ? 'active' : 'text-gray-400' }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined nav-icon" style="font-size:20px;">task_alt</span>
            <span class="nav-text text-sm">Workflow</span>
        </div>
        <span class="material-symbols-outlined submenu-arrow text-xs transition-transform duration-200 {{ request()->routeIs('staff.tasks*') ? 'rotate-180' : '' }}" style="font-size:16px;">expand_more</span>
    </button>
    <div id="workflow-menu" class="submenu pl-10 space-y-0.5 {{ request()->routeIs('staff.tasks*') ? 'open' : '' }}">
        <a href="{{ route('staff.tasks') }}" class="block py-2 text-sm text-gray-500 hover:text-[#1392EC] transition-colors {{ request()->routeIs('staff.tasks*') ? 'text-[#1392EC] font-medium' : '' }}">
            Clinic Tasks
        </a>
    </div>
</div>

<a href="{{ route('staff.certificate-requests') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('staff.certificate-requests*') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">verified</span>
    <span class="nav-text text-sm whitespace-nowrap">Certificate Requests</span>
</a>

<a href="{{ route('staff.announcements') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('staff.announcements') ? 'active' : 'text-gray-400' }}">
    <span class="material-symbols-outlined nav-icon" style="font-size:20px;">campaign</span>
    <span class="nav-text text-sm">Announcements</span>
</a>

<div class="border-t border-white/5 my-3"></div>

<a href="{{ route('staff.profile') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('staff.profile') ? 'active' : 'text-gray-400' }}">
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
