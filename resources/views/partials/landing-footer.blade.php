<footer id="landing-footer" class="relative bg-[#0F0F0F] border-t border-white/5 pt-20 pb-8 overflow-hidden opacity-0 translate-y-8 transition-all duration-700 ease-out">
    
    <!-- Decorative top gradient line -->
    <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-primary/20 to-transparent"></div>

    <!-- Background Glow Effect (Subtle) -->
    <div class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[120px] pointer-events-none translate-y-1/2"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 mb-16">
            
            <!-- Brand Column -->
            <div class="lg:col-span-6 flex flex-col items-start">
                <a href="/" class="group flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 text-primary border border-primary/10 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all duration-300 ease-out">
                        <span class="material-symbols-outlined text-[24px] transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">health_and_safety</span>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight group-hover:text-primary transition-colors duration-300">Appoint</span>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                    University of the Visayas - Toledo City Campus Clinic appointment portal.
                </p>
            </div>

            <!-- Links Columns -->
            <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-8">
                
                <!-- Column: Clinic -->
                <div>
                    <h4 class="text-white font-semibold mb-6 text-sm uppercase tracking-wider">Clinic</h4>
                    <ul class="space-y-4">
                        <li>
                            <a href="#services" class="group inline-block text-slate-400 text-sm hover:text-white transition-colors duration-200">
                                <span class="bg-gradient-to-r from-primary to-primary bg-[length:0%_1px] bg-no-repeat bg-left-bottom group-hover:bg-[length:100%_1px] transition-all duration-300 ease-out pb-0.5">Services</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('public.schedule') }}" class="group inline-block text-slate-400 text-sm hover:text-white transition-colors duration-200">
                                <span class="bg-gradient-to-r from-primary to-primary bg-[length:0%_1px] bg-no-repeat bg-left-bottom group-hover:bg-[length:100%_1px] transition-all duration-300 ease-out pb-0.5">Clinic Schedule</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column: Legal -->
                <div>
                    <h4 class="text-white font-semibold mb-6 text-sm uppercase tracking-wider">Legal</h4>
                    <ul class="space-y-4">
                        <li>
                            <a href="{{ route('legal.privacy') }}" class="group inline-block text-slate-400 text-sm hover:text-white transition-colors duration-200">
                                <span class="bg-gradient-to-r from-primary to-primary bg-[length:0%_1px] bg-no-repeat bg-left-bottom group-hover:bg-[length:100%_1px] transition-all duration-300 ease-out pb-0.5">Privacy Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('legal.terms') }}" class="group inline-block text-slate-400 text-sm hover:text-white transition-colors duration-200">
                                <span class="bg-gradient-to-r from-primary to-primary bg-[length:0%_1px] bg-no-repeat bg-left-bottom group-hover:bg-[length:100%_1px] transition-all duration-300 ease-out pb-0.5">Terms of Service</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/5 pt-8 text-center">
            <p class="text-slate-500 text-xs">
                &copy; {{ date('Y') }} UV Toledo Clinic Appointment System. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Inline script for lightweight entry animation -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const footer = document.getElementById('landing-footer');
            if (!footer) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        footer.classList.remove('opacity-0', 'translate-y-8');
                        footer.classList.add('opacity-100', 'translate-y-0');
                        observer.unobserve(footer); // Only animate once
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px'
            });

            observer.observe(footer);
        });
    </script>
</footer>