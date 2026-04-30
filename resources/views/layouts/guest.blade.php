<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — @yield('title', __('home.tagline'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- ✅ FIX: Ensure Alpine is ALWAYS available --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- ✅ FIX: Global SPA event safety --}}
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.addEventListener('change-page', (event) => {
                // optional debug (safe)
                console.log('Page changed to:', event.detail);
            });
        });
    </script>

</head>

<body class="bg-white font-sans antialiased">

    @include('components.navbar')

    {{-- Notification Toast (Alpine.js) --}}
    @if(session('success') || session('error'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 5000)" 
         x-show="show" 
         class="fixed top-5 right-5 z-[9999] max-w-sm w-full transition-all">
        
        <div @click="show = false"
             class="cursor-pointer p-4 rounded-2xl shadow-2xl border flex items-center gap-3 
             {{ session('success') ? 'bg-green-600 border-green-500' : 'bg-red-600 border-red-500' }} text-white">

            <div class="bg-white/20 rounded-full p-1">
                @if(session('success'))
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                @endif
            </div>

            <p class="text-sm font-medium">{{ session('success') ?? session('error') }}</p>
        </div>
    </div>
    @endif

    <main>
        @yield('content')
    </main>

    @include('components.footer')

</body>
</html>