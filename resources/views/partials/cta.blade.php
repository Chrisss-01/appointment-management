<section class="relative w-full bg-[#0F0F0F] py-24 px-6 overflow-hidden">
    <!-- Center Container -->
    <div class="max-w-5xl mx-auto relative z-10" data-reveal="cta">
        <!-- Card Wrapper -->
        <div class="group relative w-full overflow-hidden rounded-3xl border border-white/10 bg-[#121212] shadow-2xl transition-transform duration-500 hover:-translate-y-1">
            
            <!-- 1. Background Layers -->
            <!-- Base dark fill to ensure opacity -->
            <div class="absolute inset-0 bg-[#121212] z-0"></div>
            
            <!-- Top-Right Blue Glow (Subtle) -->
            <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-[#1392ec]/10 blur-[100px] pointer-events-none z-0"></div>
            
            <!-- Bottom Dark Vignette -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent z-0 pointer-events-none"></div>

            <!-- 2. Content Container -->
            <div class="relative z-10 flex flex-col items-center justify-center px-8 py-16 text-center md:px-16 md:py-20">
                
                <!-- Title -->
                <h2 class="mb-6 text-3xl font-bold tracking-tight text-white md:text-4xl lg:text-5xl">
                    Ready to book your appointment?
                </h2>
                
                <!-- Subtitle -->
                <p class="mb-10 max-w-2xl text-lg text-slate-400 leading-relaxed font-medium">
                    Schedule a visit with your school clinic in minutes — fast, secure, and hassle-free.
                </p>
                
                <!-- Buttons Row -->
                <div class="flex flex-col w-full sm:w-auto sm:flex-row items-center gap-4">
                    <!-- Primary Button -->
                    <a href="/appointments/create" 
                       class="flex h-12 w-full sm:w-auto items-center justify-center rounded-lg bg-[#1392ec] px-8 text-base font-bold text-white transition-all duration-300 hover:bg-[#1180cf] shadow-[0_0_20px_-5px_rgba(19,146,236,0.4)] hover:shadow-[0_0_25px_-5px_rgba(19,146,236,0.6)]">
                        Book Appointment
                    </a>
                    
                    <!-- Secondary Button -->
                    <a href="/login" 
                       class="flex h-12 w-full sm:w-auto items-center justify-center rounded-lg border border-white/20 bg-transparent px-8 text-base font-bold text-white transition-all duration-300 hover:bg-white/5">
                        Log In
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Progressive Reveal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.remove('opacity-0', 'translate-y-8');
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const ctaElement = document.querySelector('[data-reveal="cta"]');
            if (ctaElement) {
                // Initial state
                ctaElement.classList.add('transition-all', 'duration-700', 'ease-out', 'opacity-0', 'translate-y-8');
                observer.observe(ctaElement);
            }
        });
    </script>
</section>