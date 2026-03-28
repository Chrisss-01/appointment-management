<nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/5 bg-[#0F0F0F]/80 backdrop-blur-xl transition-all duration-300" aria-label="Main navigation">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        
        <!-- Logo / Brand -->
        <a href="{{ Route::has('landing') ? route('landing') : '#' }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-[#1392ec] rounded-lg p-1">
            <div class="flex items-center justify-center w-8 h-8 rounded bg-[#1392ec]/20 text-[#1392ec] group-hover:scale-105 group-hover:rotate-3 transition-transform duration-300">
                <span class="material-symbols-outlined" style="font-size: 20px;">health_and_safety</span>
            </div>
            <span class="text-white text-xl font-bold tracking-tight">Appoint</span>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="relative py-1 text-slate-300 hover:text-white text-sm font-medium transition-colors group focus:outline-none focus:text-white">
                Features
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#1392ec] transition-all duration-300 ease-out group-hover:w-full"></span>
            </a>
            <a href="#how-it-works" class="relative py-1 text-slate-300 hover:text-white text-sm font-medium transition-colors group focus:outline-none focus:text-white">
                How it Works
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#1392ec] transition-all duration-300 ease-out group-hover:w-full"></span>
            </a>
            <a href="#landing-footer" class="relative py-1 text-slate-300 hover:text-white text-sm font-medium transition-colors group focus:outline-none focus:text-white">
                Contact
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#1392ec] transition-all duration-300 ease-out group-hover:w-full"></span>
            </a>
        </div>

        <!-- Desktop Actions -->
        <div class="hidden md:flex items-center gap-6">
            <a href="{{ Route::has('login') ? route('login') : '/login' }}" class="text-slate-300 hover:text-white text-sm font-medium transition-colors focus:outline-none focus:underline decoration-[#1392ec] decoration-2 underline-offset-4">
                Log in
            </a>
            <a href="{{ Route::has('student.appointments.create') ? route('student.appointments.create') : '#' }}" class="group relative flex items-center justify-center rounded-lg h-10 px-5 bg-[#1392ec] hover:bg-blue-500 text-white text-sm font-semibold shadow-[0_0_20px_-5px_rgba(19,146,236,0.4)] hover:shadow-[0_0_25px_-5px_rgba(19,146,236,0.6)] transition-all duration-300 ease-out hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#0F0F0F] focus:ring-[#1392ec]">
                <span>Book Now</span>
            </a>
        </div>

        <!-- Mobile Menu Button -->
        <button id="mobile-menu-btn" type="button" class="md:hidden flex items-center justify-center text-slate-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-[#1392ec] rounded-lg p-2 transition-colors" aria-expanded="false" aria-controls="mobile-menu" aria-label="Toggle navigation menu">
            <span class="material-symbols-outlined text-2xl" id="menu-icon">menu</span>
            <span class="material-symbols-outlined text-2xl hidden" id="close-icon">close</span>
        </button>
    </div>

    <!-- Mobile Menu Panel -->
    <div id="mobile-menu" class="hidden md:hidden absolute top-20 left-0 right-0 bg-[#0F0F0F] border-b border-white/5 shadow-2xl origin-top transition-all duration-200">
        <div class="px-6 py-8 space-y-6 flex flex-col">
            <a href="#features" class="text-slate-300 hover:text-[#1392ec] text-base font-medium transition-colors mobile-link">Features</a>
            <a href="#how-it-works" class="text-slate-300 hover:text-[#1392ec] text-base font-medium transition-colors mobile-link">How it Works</a>
            <a href="#contact" class="text-slate-300 hover:text-[#1392ec] text-base font-medium transition-colors mobile-link">Contact</a>
            
            <div class="h-px bg-white/10 my-4"></div>
            
            <div class="flex flex-col gap-4">
                <a href="{{ Route::has('login') ? route('login') : '#' }}" class="flex items-center justify-center h-12 rounded-lg border border-white/10 hover:bg-white/5 text-slate-300 hover:text-white font-medium transition-colors">
                    Log in
                </a>
                <a href="{{ Route::has('student.appointments.create') ? route('student.appointments.create') : '#' }}" class="flex items-center justify-center h-12 rounded-lg bg-[#1392ec] hover:bg-blue-500 text-white font-bold shadow-lg shadow-[#1392ec]/20 hover:shadow-[#1392ec]/40 transition-all">
                    Book Appointment
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');
    const links = document.querySelectorAll('.mobile-link');

    // 🛑 Safety check — exit if navbar is not present
    if (!btn || !menu || !menuIcon || !closeIcon) return;

    let isOpen = false;

    function toggleMenu() {
        isOpen = !isOpen;

        if (isOpen) {
            menu.classList.remove('hidden');
            menuIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
        } else {
            menu.classList.add('hidden');
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }
    }

    btn.addEventListener('click', toggleMenu);

    links.forEach(link => {
        link.addEventListener('click', () => {
            if (isOpen) toggleMenu();
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768 && isOpen) {
            toggleMenu();
        }
    });
});
</script>