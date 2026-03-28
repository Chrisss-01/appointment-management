<section class="py-24 relative overflow-hidden bg-[#0F0F0F]" id="how-it-works">
    {{-- Center Glow --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[300px] bg-[#1392ec]/5 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        {{-- Heading --}}
        <div class="text-center max-w-2xl mx-auto mb-16 opacity-0 translate-y-4 transition-all duration-700 ease-out" id="workflow-heading">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Simple, Streamlined Workflow</h2>
            <p class="text-slate-400">Get started in minutes. Our platform is designed for zero learning curve.</p>
        </div>

        <div class="relative">
            {{-- Desktop Line --}}
            <div class="hidden md:block absolute top-12 left-0 right-0 h-0.5 bg-white/10 -z-10 w-3/4 mx-auto"></div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                {{-- Step 1 --}}
                <div class="flex flex-col items-center text-center group opacity-0 translate-y-4 transition-all duration-700 ease-out workflow-step" style="transition-delay: 100ms;">
                    <div class="w-24 h-24 rounded-2xl bg-[#1A1A1A] border border-white/10 flex items-center justify-center mb-6 relative shadow-lg group-hover:border-[#1392ec]/50 transition-all duration-300 group-hover:scale-[1.03]">
                        <span class="absolute -top-3 -right-3 w-8 h-8 bg-[#1392ec] rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md border-4 border-[#0F0F0F]">1</span>
                        <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-[#1392ec] transition-colors duration-300">person_add</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Create Profile</h3>
                    <p class="text-slate-400 text-sm max-w-xs">Register your student account securely with your school email.</p>
                </div>

                {{-- Step 2 --}}
                <div class="flex flex-col items-center text-center group opacity-0 translate-y-4 transition-all duration-700 ease-out workflow-step" style="transition-delay: 200ms;">
                    <div class="w-24 h-24 rounded-2xl bg-[#1A1A1A] border border-white/10 flex items-center justify-center mb-6 relative shadow-lg group-hover:border-[#1392ec]/50 transition-all duration-300 group-hover:scale-[1.03]">
                        <span class="absolute -top-3 -right-3 w-8 h-8 bg-[#1392ec] rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md border-4 border-[#0F0F0F]">2</span>
                        <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-[#1392ec] transition-colors duration-300">event_available</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Choose Service</h3>
                    <p class="text-slate-400 text-sm max-w-xs">Select the type of check-up and find a time slot that fits your schedule.</p>
                </div>

                {{-- Step 3 --}}
                <div class="flex flex-col items-center text-center group opacity-0 translate-y-4 transition-all duration-700 ease-out workflow-step" style="transition-delay: 300ms;">
                    <div class="w-24 h-24 rounded-2xl bg-[#1A1A1A] border border-white/10 flex items-center justify-center mb-6 relative shadow-lg group-hover:border-[#1392ec]/50 transition-all duration-300 group-hover:scale-[1.03]">
                        <span class="absolute -top-3 -right-3 w-8 h-8 bg-[#1392ec] rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md border-4 border-[#0F0F0F]">3</span>
                        <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-[#1392ec] transition-colors duration-300">assignment</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Submit Request</h3>
                    <p class="text-slate-400 text-sm max-w-xs">Submit your appointment request and receive confirmation once approved by the clinic.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.remove('opacity-0', 'translate-y-4');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const heading = document.getElementById('workflow-heading');
            if (heading) observer.observe(heading);

            const steps = document.querySelectorAll('.workflow-step');
            steps.forEach(step => observer.observe(step));
        });
    </script>
</section>