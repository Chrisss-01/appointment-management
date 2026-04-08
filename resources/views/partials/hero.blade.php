<div>
<section class="relative overflow-hidden bg-[#0F0F0F] py-20 lg:py-32" id="hero-section">
    {{-- Background Blobs --}}
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-[#1392ec]/10 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-[#1392ec]/5 rounded-full blur-[100px] translate-y-1/2 -translate-x-1/3 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center relative z-10">
        {{-- Left Column --}}
        <div class="flex flex-col gap-8 max-w-2xl">
            {{-- Headline --}}
            <h1 class="reveal-item opacity-0 translate-y-8 transition-all duration-700 ease-out text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.1] text-white">
                Smart <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1392ec] to-blue-300">Clinic</span> <br>
                Management.
            </h1>

            {{-- Paragraph --}}
            <p class="reveal-item delay-100 opacity-0 translate-y-8 transition-all duration-700 ease-out text-lg text-slate-400 max-w-lg leading-relaxed">
                Book your UV Toledo clinic appointments online. Skip the queues with instant scheduling and real-time availability.
            </p>

            {{-- Buttons --}}
            <div class="reveal-item delay-200 opacity-0 translate-y-8 transition-all duration-700 ease-out flex flex-wrap gap-4 pt-2">
                <a 
                    href="{{ route('student.services') }}" 
                    class="group relative flex items-center justify-center rounded-lg h-14 px-8 bg-[#1392ec] text-white text-base font-bold transition-all duration-300 shadow-lg hover:shadow-[0_0_40px_-5px_rgba(19,146,236,0.6)] hover:-translate-y-1 overflow-hidden"
                >
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    <span class="relative z-10">Book Appointment</span>
                </a>
                <a 
                    href="{{ Route::has('login') ? route('login') : '/login' }}" 
                    class="flex items-center justify-center rounded-lg h-14 px-8 bg-white/5 border border-white/10 text-white text-base font-bold transition-all duration-300 backdrop-blur-sm hover:bg-white/10 hover:border-white/30 hover:shadow-[0_0_20px_-5px_rgba(255,255,255,0.1)] hover:-translate-y-1"
                >
                    Log In
                </a>
            </div>

            {{-- Micro-copy with Avatars --}}
            <div class="reveal-item delay-300 opacity-0 translate-y-8 transition-all duration-700 ease-out flex items-center gap-4 pt-2 text-sm text-slate-500">
                <div class="flex -space-x-2">
                    <div class="w-8 h-8 rounded-full bg-slate-700 border-2 border-[#0F0F0F] hover:z-10 hover:scale-110 transition-transform duration-300 cursor-default"></div>
                    <div class="w-8 h-8 rounded-full bg-slate-600 border-2 border-[#0F0F0F] hover:z-10 hover:scale-110 transition-transform duration-300 cursor-default"></div>
                    <div class="w-8 h-8 rounded-full bg-slate-500 border-2 border-[#0F0F0F] hover:z-10 hover:scale-110 transition-transform duration-300 cursor-default"></div>
                </div>
                <p>Built for the University of the Visayas - Toledo City Campus Clinic.</p>
            </div>
        </div>

        {{-- Right Column: Dashboard Preview --}}
        <div class="reveal-item delay-500 opacity-0 translate-y-8 transition-all duration-1000 ease-out relative lg:h-[600px] w-full flex items-center justify-center group">
            {{-- Card Glow --}}
            <div class="absolute inset-0 bg-gradient-to-tr from-[#1392ec]/20 to-transparent rounded-[2rem] transform rotate-3 scale-95 opacity-50 blur-2xl pointer-events-none transition-opacity duration-500 group-hover:opacity-70"></div>
            
            {{-- Card Container --}}
            <div class="relative w-full aspect-[4/3] lg:aspect-auto lg:h-full bg-[#1A1A1A] border border-white/10 rounded-[2rem] overflow-hidden shadow-2xl flex flex-col transition-all duration-500 ease-out group-hover:-translate-y-2 group-hover:shadow-[0_30px_60px_-15px_rgba(19,146,236,0.25)] group-hover:border-white/20">
                {{-- Window Controls --}}
                <div class="h-10 border-b border-white/5 bg-white/5 flex items-center px-4 gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/50 group-hover:bg-red-500 transition-colors duration-300"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/50 group-hover:bg-yellow-500 transition-colors duration-300"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/50 group-hover:bg-green-500 transition-colors duration-300"></div>
                </div>

                {{-- Dashboard Mockup Content --}}
                <div class="flex-1 p-6 bg-[#1A1A1A] relative overflow-hidden">
                    {{-- Sidebar --}}
                    <div class="absolute left-0 top-0 bottom-0 w-16 border-r border-white/5 flex flex-col items-center py-6 gap-6">
                        <div class="w-8 h-8 rounded bg-[#1392ec]/20 group-hover:bg-[#1392ec]/30 transition-colors duration-300"></div>
                        <div class="w-8 h-8 rounded bg-white/5"></div>
                        <div class="w-8 h-8 rounded bg-white/5"></div>
                        <div class="w-8 h-8 rounded bg-white/5"></div>
                    </div>

                    {{-- Main Content Area --}}
                    <div class="ml-16 h-full flex flex-col gap-6">
                        {{-- Header Placeholder --}}
                        <div class="h-8 w-48 bg-white/10 rounded-full"></div>
                        
                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="h-32 bg-white/5 rounded-xl border border-white/5 p-4 flex flex-col justify-between hover:bg-white/10 transition-colors duration-300">
                                <div class="w-8 h-8 rounded-full bg-[#1392ec]/20"></div>
                                <div class="h-2 w-24 bg-white/10 rounded"></div>
                            </div>
                            <div class="h-32 bg-white/5 rounded-xl border border-white/5 p-4 flex flex-col justify-between hover:bg-white/10 transition-colors duration-300">
                                <div class="w-8 h-8 rounded-full bg-purple-500/20"></div>
                                <div class="h-2 w-24 bg-white/10 rounded"></div>
                            </div>
                        </div>

                        {{-- List Placeholder --}}
                        <div class="flex-1 bg-white/5 rounded-xl border border-white/5 p-4">
                            <div class="flex justify-between mb-4">
                                <div class="h-4 w-32 bg-white/10 rounded"></div>
                                <div class="h-4 w-16 bg-[#1392ec]/20 rounded"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-10 w-full bg-white/5 rounded"></div>
                                <div class="h-10 w-full bg-white/5 rounded"></div>
                                <div class="h-10 w-full bg-white/5 rounded"></div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Fade Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0F0F0F] via-transparent to-transparent opacity-40 pointer-events-none"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Styles for Shimmer Animation --}}
    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
    </style>

    {{-- Progressive Reveal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hero = document.getElementById('hero-section');
            if (hero) {
                const items = hero.querySelectorAll('.reveal-item');
                // Small delay to ensure paint
                setTimeout(() => {
                    items.forEach(item => {
                        item.classList.remove('opacity-0', 'translate-y-8');
                    });
                }, 100);
            }
        });
    </script>
</section>
</div>