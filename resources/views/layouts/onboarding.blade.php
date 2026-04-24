<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'Complete Your Profile') | UV Toledo Clinic</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#0F0F0F] text-white antialiased [font-family:'Inter',sans-serif]">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-white/5">
            <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6">
                <a class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1392EC] shadow-[0_0_20px_-5px_rgba(19,146,236,0.45)]">
                        <span class="material-symbols-outlined text-[20px] text-white">health_and_safety</span>
                    </div>
                    <span class="text-xl font-bold tracking-tight">Appoint</span>
                </a>

                <div class="relative" id="userMenuWrapper">
                    <button
                        type="button"
                        id="userMenuButton"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-300 transition-colors hover:bg-white/5 hover:text-white"
                        aria-expanded="false"
                    >
                        <span>{{ auth()->user()->email ?? 'student@uv.edu.ph' }}</span>
                        <span
                            id="userMenuChevron"
                            class="material-symbols-outlined text-[18px] transition-transform duration-200"
                        >
                            expand_more
                        </span>
                    </button>

                    <!-- Dropdown -->
                    <div
                        id="userMenuDropdown"
                        class="absolute right-0 mt-2 hidden min-w-[160px] overflow-hidden rounded-xl border border-white/10 bg-[#1A1A1A] shadow-lg z-50"
                    >
                        <form method="POST" action="{{ route('logout') }}" class="w-full m-0 p-0">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center gap-3 px-4 py-3 text-sm text-slate-300 transition-colors hover:bg-white/5 hover:text-white"
                            >
                                <span class="material-symbols-outlined text-[18px]">logout</span>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('userMenuWrapper');
        const button = document.getElementById('userMenuButton');
        const dropdown = document.getElementById('userMenuDropdown');
        const chevron = document.getElementById('userMenuChevron');

        if (!wrapper || !button || !dropdown) return;

            button.addEventListener('click', function (e) {
                e.stopPropagation();

                const isHidden = dropdown.classList.contains('hidden');

        if (isHidden) {
            dropdown.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            dropdown.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    });

        document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) {
            dropdown.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    });
});
</script>
</body>
</html>
