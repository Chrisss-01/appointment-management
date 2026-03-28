<section id="features" class="py-20 bg-[#0F0F0F] relative border-y border-white/5 overflow-hidden">
    <!-- Strip overlay -->
    <div class="absolute inset-0 bg-[#1A1A1A]/30 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <!-- Header Row -->
        <div class="feature-header flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16 opacity-0 translate-y-6 transition-all duration-500 ease-out">
            <div class="max-w-xl">
                <h2 class="text-[#1392ec] font-semibold mb-2 tracking-wide uppercase text-sm">Key Features</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-white tracking-tight">Clinic services and features at a glance.</h3>
            </div>
            <p class="text-slate-400 max-w-sm text-sm md:text-base">
                A quick overview of available services and scheduling options for UV Toledo students.
            </p>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="feature-card group p-6 rounded-2xl bg-[#1A1A1A] border border-white/5 hover:border-[#1392ec]/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_40px_-20px_rgba(19,146,236,0.35)] opacity-0 translate-y-6">
                <div class="w-12 h-12 rounded-xl bg-[#1392ec]/10 text-[#1392ec] flex items-center justify-center mb-6 group-hover:bg-[#1392ec] group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl transition-transform duration-300 group-hover:scale-105 group-hover:rotate-3">stethoscope</span>
                </div>
                <h4 class="text-xl font-bold text-white mb-3">Medical Consultation</h4>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Schedule consultations for general check-ups, illness consultations, and medical advice.
                </p>
            </div>

            <!-- Card 2 -->
            <div class="feature-card group p-6 rounded-2xl bg-[#1A1A1A] border border-white/5 hover:border-[#1392ec]/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_40px_-20px_rgba(19,146,236,0.35)] opacity-0 translate-y-6" style="transition-delay: 100ms;">
                <div class="w-12 h-12 rounded-xl bg-[#1392ec]/10 text-[#1392ec] flex items-center justify-center mb-6 group-hover:bg-[#1392ec] group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl transition-transform duration-300 group-hover:scale-105 group-hover:rotate-3">monitor_heart</span>
                </div>
                <h4 class="text-xl font-bold text-white mb-3">Medical Examination</h4>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Submit your medical exam results for evaluation and receive your medical certificate.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="feature-card group p-6 rounded-2xl bg-[#1A1A1A] border border-white/5 hover:border-[#1392ec]/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_40px_-20px_rgba(19,146,236,0.35)] opacity-0 translate-y-6" style="transition-delay: 200ms;">
                <div class="w-12 h-12 rounded-xl bg-[#1392ec]/10 text-[#1392ec] flex items-center justify-center mb-6 group-hover:bg-[#1392ec] group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl transition-transform duration-300 group-hover:scale-105 group-hover:rotate-3">dentistry</span>
                </div>
                <h4 class="text-xl font-bold text-white mb-3">Dental Consultation</h4>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Book dental check-ups and treatments on Tuesdays and Thursdays.
                </p>
            </div>

            <!-- Card 4 -->
            <div class="feature-card group p-6 rounded-2xl bg-[#1A1A1A] border border-white/5 hover:border-[#1392ec]/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_40px_-20px_rgba(19,146,236,0.35)] opacity-0 translate-y-6" style="transition-delay: 300ms;">
                <div class="w-12 h-12 rounded-xl bg-[#1392ec]/10 text-[#1392ec] flex items-center justify-center mb-6 group-hover:bg-[#1392ec] group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-3xl transition-transform duration-300 group-hover:scale-105 group-hover:rotate-3">calendar_month</span>
                </div>
                <h4 class="text-xl font-bold text-white mb-3">Automated Scheduling</h4>
                <p class="text-slate-400 text-sm leading-relaxed">
                    See available time slots andpick your preferred schedule.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const section = document.getElementById('features');
            if (!section) return;

            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const header = section.querySelector('.feature-header');
                        const cards = section.querySelectorAll('.feature-card');

                        if (header) {
                            header.classList.remove('opacity-0', 'translate-y-6');
                        }

                        cards.forEach((card, index) => {
                            // Use the inline transition-delay if present, otherwise fallback
                            card.classList.remove('opacity-0', 'translate-y-6');
                        });

                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            observer.observe(section);
        });
    </script>
</section>