<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | UV Toledo Clinic</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        * { font-family: 'Inter', sans-serif; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* Sidebar nav transitions */
        .nav-link { transition: all 0.2s ease; }
        .nav-link:hover { background: rgba(19, 146, 236, 0.1); }
        .nav-link.active { background: rgba(19, 146, 236, 0.15); border-right: 3px solid #1392EC; }
        .nav-link.active .nav-icon { color: #1392EC; }
        .nav-link.active .nav-text { color: #1392EC; font-weight: 600; }

        /* Submenu animation */
        .submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .submenu.open { max-height: 300px; }

        /* Card hover */
        .card-hover { transition: all 0.2s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.3); }

        /* Badge pulse */
        .badge-pulse { animation: pulse-blue 2s infinite; }
        @keyframes pulse-blue {
            0%, 100% { box-shadow: 0 0 0 0 rgba(19,146,236,0.4); }
            50% { box-shadow: 0 0 0 6px rgba(19,146,236,0); }
        }

        /* Page transition */
        .page-enter { animation: fadeInUp 0.3s ease forwards; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Notification dropdown */
        .notification-dropdown { display: none; }
        .notification-dropdown.show { display: block; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-[#0F0F0F] text-gray-200 antialiased">
    <div class="flex h-screen overflow-hidden">

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- SIDEBAR --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#141414] border-r border-white/5 flex flex-col transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">

            {{-- Brand --}}
            <div class="px-5 py-5 border-b border-white/5">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-[#1392EC] to-[#1392EC] rounded-xl flex items-center justify-center shadow-lg shadow-[#1392EC]/20">
                        <span class="material-symbols-outlined text-white text-lg" style="font-size:20px;">local_hospital</span>
                    </div>
                    <div>
                        <span class="text-lg font-bold text-white tracking-tight">UV Clinic</span>
                        <span class="block text-[10px] text-[#1392EC]/60 uppercase tracking-widest font-medium -mt-0.5">Health Services</span>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                @yield('sidebar')
            </nav>

            {{-- User Card --}}
            <div class="border-t border-white/5 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#1392EC]/30 to-[#1392EC]/50 border border-[#1392EC]/20 flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-[11px] text-gray-500 capitalize">{{ auth()->user()->role ?? 'guest' }}</p>
                    </div>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors" title="Sign Out">
                            <span class="material-symbols-outlined" style="font-size:18px;">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- MAIN CONTENT --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top Bar --}}
            <header class="h-16 bg-[#141414]/80 backdrop-blur-xl border-b border-white/5 flex items-center justify-between px-4 lg:px-6 shrink-0 relative z-50">
                {{-- Mobile menu toggle --}}
                <button id="sidebar-toggle" class="lg:hidden text-gray-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>

                {{-- Page Title --}}
                <div class="hidden lg:block">
                    <h1 class="text-lg font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-3">
                    {{-- Notifications --}}
                    <div class="relative" id="notification-wrapper">
                        <button id="notification-btn" class="relative p-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all">
                            <span class="material-symbols-outlined" style="font-size:22px;">notifications</span>
                            <span id="notification-badge" class="hidden absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#1392EC] rounded-full text-[10px] font-bold text-white flex items-center justify-center badge-pulse"></span>
                        </button>

                        {{-- Notification Dropdown --}}
                        <div id="notification-dropdown" class="notification-dropdown absolute right-0 top-12 w-80 bg-[#1A1A1A] border border-white/10 rounded-2xl shadow-2xl shadow-black/50 overflow-hidden z-[100]">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-white/5">
                                <span class="text-sm font-semibold text-white">Notifications</span>
                                <button id="mark-all-read" class="text-xs text-[#1392EC] hover:opacity-80 transition-colors">Mark all read</button>
                            </div>
                            <div id="notification-list" class="max-h-72 overflow-y-auto divide-y divide-white/5">
                                <div class="px-4 py-6 text-center text-gray-500 text-sm">No notifications</div>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile: page title --}}
                    <span class="lg:hidden text-sm font-semibold text-white">@yield('page-title', 'Dashboard')</span>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mx-4 lg:mx-6 mt-4 px-4 py-3 bg-[#1392EC]/10 border border-[#1392EC]/20 rounded-xl text-[#1392EC] text-sm flex items-center gap-2" id="flash-success">
                <span class="material-symbols-outlined" style="font-size:18px;">check_circle</span>
                {{ session('success') }}
                <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-[#1392EC]/50 hover:text-[#1392EC]">
                    <span class="material-symbols-outlined" style="font-size:16px;">close</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-4 lg:mx-6 mt-4 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center gap-2" id="flash-error">
                <span class="material-symbols-outlined" style="font-size:18px;">error</span>
                {{ session('error') }}
                <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-400/50 hover:text-red-400">
                    <span class="material-symbols-outlined" style="font-size:16px;">close</span>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="mx-4 lg:mx-6 mt-4 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 page-enter">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Sidebar Overlay (mobile) --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggle = document.getElementById('sidebar-toggle');

        toggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        // Submenu toggle
        document.querySelectorAll('[data-submenu]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.getElementById(btn.dataset.submenu);
                const arrow = btn.querySelector('.submenu-arrow');
                target?.classList.toggle('open');
                arrow?.classList.toggle('rotate-180');
            });
        });

        // Notification toggle
        const notifBtn = document.getElementById('notification-btn');
        const notifDropdown = document.getElementById('notification-dropdown');
        notifBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown?.classList.toggle('show');
        });
        document.addEventListener('click', (e) => {
            if (!document.getElementById('notification-wrapper')?.contains(e.target)) {
                notifDropdown?.classList.remove('show');
            }
        });

        // Load notifications
        async function loadNotifications() {
            try {
                const res = await fetch('/api/notifications', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }});
                const data = await res.json();
                const badge = document.getElementById('notification-badge');
                const list = document.getElementById('notification-list');

                if (data.unread_count > 0) {
                    badge?.classList.remove('hidden');
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                } else {
                    badge?.classList.add('hidden');
                }

                if (data.notifications?.length > 0) {
                    list.innerHTML = data.notifications.map(n => `
                        <div class="px-4 py-3 hover:bg-white/5 transition-colors cursor-pointer ${!n.read_at ? 'bg-[#1392EC]/5' : ''}" onclick="markRead('${n.id}', this)">
                            <p class="text-sm text-white font-medium">${n.title}</p>
                            <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">${n.message}</p>
                            <p class="text-[10px] text-gray-600 mt-1">${new Date(n.created_at).toLocaleDateString()}</p>
                        </div>
                    `).join('');
                }
            } catch(e) {}
        }

        async function markRead(id, el) {
            try {
                await fetch(`/api/notifications/${id}/read`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }});
                el?.classList.remove('bg-[#1392EC]/5');
                loadNotifications();
            } catch(e) {}
        }

        document.getElementById('mark-all-read')?.addEventListener('click', async () => {
            await fetch('/api/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }});
            loadNotifications();
        });

        loadNotifications();
        setInterval(loadNotifications, 30000);

        // Auto-dismiss flash messages
        setTimeout(() => { document.getElementById('flash-success')?.remove(); }, 5000);
        setTimeout(() => { document.getElementById('flash-error')?.remove(); }, 5000);
    </script>
    @stack('scripts')
    @stack('modals')
</body>
</html>
