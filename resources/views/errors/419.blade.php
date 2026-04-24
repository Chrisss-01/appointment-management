<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Expired | UV Toledo Clinic</title>

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-[#0F0F0F] text-white antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-12">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(19,146,236,0.12),transparent_42%)]"></div>

        <section class="relative w-full max-w-lg rounded-3xl border border-white/10 bg-[#141414] p-8 shadow-2xl shadow-black/40 sm:p-10">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#1392EC]">419 Error</p>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-4xl">Page Expired</h1>
            <p class="mt-3 text-sm leading-6 text-gray-400 sm:text-base">
                Your session expired. Please go back and try again.
            </p>

            <a
                href="{{ url()->previous() }}"
                class="mt-8 inline-flex items-center justify-center rounded-xl bg-[#1392EC] px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#1181d1] focus:outline-none focus:ring-2 focus:ring-[#1392EC]/60 focus:ring-offset-2 focus:ring-offset-[#141414]"
            >
                Go Back
            </a>
        </section>
    </main>
</body>
</html>
