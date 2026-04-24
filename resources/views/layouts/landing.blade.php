<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>School Clinic Appointment System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap">
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col">

<div class="min-h-screen bg-[#0F0F0F] text-white relative overflow-hidden">
    {{-- Top Navigation --}}
    @include('partials.landing-nav')

    {{-- Page Content --}}
    <main class="flex-1 pt-20">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.landing-footer')
</div>
    
</body>
</html>
