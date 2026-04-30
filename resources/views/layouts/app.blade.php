<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BloodConnect Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: false }" class="font-sans antialiased bg-[#F7F7F7] text-gray-900">

    {{-- 1. PROVIDER APPROVAL CHECK --}}
    @auth
        @if(Auth::user()->role === 'doctor' && Auth::user()->is_active == 0)
            <script>
                alert('Your account is pending approval. You will be logged out.');
                window.location = "{{ route('login') }}";
            </script>
            @php
                Auth::logout();
                session()->flash('error', 'Your account is pending approval. Please contact the Admin.');
            @endphp
        @endif
    @endauth

    <div class="flex min-h-screen">
        
        {{-- 2. Sidebar --}}
        @auth
        {{-- Mobile Overlay --}}
        <div 
            x-show="sidebarOpen" 
            x-transition.opacity
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-black/50 z-40 md:hidden">
        </div>

        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="w-64 bg-[#2A3F54] text-[#ECF0F1] flex flex-col fixed inset-y-0 left-0 z-50 shadow-2xl transition-transform duration-300 ease-in-out">
            
            <div class="p-5 border-b border-[#3E4F5F] flex items-center justify-between bg-[#233545]">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-droplet text-red-500 text-2xl animate-pulse"></i>
                    <span class="text-xl font-bold tracking-tight text-white uppercase">BloodConnect</span>
                </div>
                {{-- Mobile Close Button --}}
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- User Profile Section --}}
            <div class="p-6 border-b border-[#3E4F5F] text-center bg-[#2A3F54]">
                <div class="relative inline-block">
                    <img src="{{ Auth::user()->profile_photo ? asset('storage/'.Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->first_name).'&background=EF4444&color=fff' }}" 
                         class="h-24 w-24 rounded-full border-4 border-[#3E4F5F] object-cover shadow-xl mx-auto">
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 rounded-full border-4 border-[#2A3F54]"></span>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ __('dashboard.sidebar.welcome_back') }}</p>
                    <p class="font-bold text-lg text-white mt-1">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <span class="text-[10px] font-bold text-red-400 bg-red-500/10 border border-red-500/20 px-3 py-1 rounded-full mt-2 inline-block uppercase tracking-tighter">
                        {{ Auth::user()->role === 'doctor' ? __('dashboard.sidebar.provider') : Auth::user()->role }}
                    </span>
                </div>
            </div>

            <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scrollbar pt-4">
                
                <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">{{ __('dashboard.sidebar.main_menu') }}</p>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-[#3E4F5F] text-white' : '' }}">
                    <i class="fa-solid fa-house w-5 text-center text-blue-400"></i> {{ __('dashboard.sidebar.home') }}
                </a>

                {{-- PROVIDER SPECIFIC ACTIONS --}}
                @if(Auth::user()->role === 'doctor')
                    <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-6 mb-2">{{ __('dashboard.sidebar.provider_control') }}</p>
                    
                    <a href="{{ route('doctor.appointments') }}" class="flex items-center justify-between px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('doctor.appointments') ? 'bg-[#3E4F5F] text-white' : '' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-calendar-check w-5 text-center text-red-400"></i> 
                            <span>{{ __('dashboard.sidebar.appointments') }}</span>
                        </div>
                        <span class="bg-red-500/20 text-red-400 text-[10px] px-2 py-0.5 rounded-full font-bold border border-red-500/30">
                            {{ $stats['pending'] ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('doctor.users.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('doctor.users.index') ? 'bg-[#3E4F5F] text-white' : '' }}">
                        <i class="fa-solid fa-users w-5 text-center text-green-400"></i> {{ __('dashboard.sidebar.manage_donors') }}
                    </a>

                    <a href="{{ route('doctor.donations') }}" class="flex items-center justify-between px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('doctor.donations') ? 'bg-[#3E4F5F] text-white' : '' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clock-rotate-left w-5 text-center text-yellow-400"></i> 
                            <span>{{ __('dashboard.sidebar.donation_history') }}</span>
                        </div>
                    </a>

                    <a href="{{ route('doctor.feedback') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('doctor.feedback') ? 'bg-[#3E4F5F] text-white' : '' }}">
                        <i class="fa-solid fa-comment-medical w-5 text-center text-pink-400"></i> {{ __('dashboard.sidebar.feedback') }}
                    </a>

                    <a href="{{ route('doctor.reports') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('doctor.reports') ? 'bg-[#3E4F5F] text-white' : '' }}">
                        <i class="fa-solid fa-file-invoice w-5 text-center text-indigo-400"></i> {{ __('dashboard.sidebar.reports') }}
                    </a>
                @endif

                {{-- DONOR SPECIFIC QUICK ACTIONS - UPDATED TO NEW ROUTES --}}
                @if(Auth::user()->role === 'donor')
                    <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-6 mb-2">{{ __('dashboard.sidebar.quick_access') }}</p>
                    
                    <a href="{{ route('donor.locations') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('donor.locations') ? 'bg-[#3E4F5F] text-white' : '' }}">
                        <i class="fa-solid fa-map-location-dot w-5 text-center text-red-400"></i> {{ __('dashboard.sidebar.find_locations') }}
                    </a>

                  <a href="{{ route('donor.eligibility') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('donor.eligibility') ? 'bg-[#3E4F5F] text-white' : '' }}">
    <i class="fa-solid fa-newspaper w-5 text-center text-blue-400"></i> 
    {{ __('dashboard.sidebar.eligibility_guide') }}
</a>

                    <a href="{{ route('appointments.create') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition">
                        <i class="fa-solid fa-calendar-plus w-5 text-center text-green-400"></i> {{ __('dashboard.sidebar.book_appointment') }}
                    </a>

                    <a href="{{ route('contact.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition">
                        <i class="fa-solid fa-envelope w-5 text-center text-yellow-400"></i> {{ __('dashboard.sidebar.reach_us') }}
                    </a>
                @endif

                {{-- ADMIN SPECIFIC ACTIONS --}}
                @if(Auth::user()->role === 'admin')
                    <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-6 mb-2">{{ __('dashboard.sidebar.admin_panel') }}</p>
                    
                    <a href="{{ route('admin.centers.index') }}" class="flex items-center justify-between px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition group">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-location-dot w-5 text-center text-red-400"></i> 
                            <span>{{ __('dashboard.sidebar.manage_locations') }}</span>
                        </div>
                        <span class="bg-red-500/20 text-red-400 text-[10px] px-2 py-0.5 rounded-full font-bold border border-red-500/30">
                            {{ $stats['total_locations'] ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('admin.doctors.index') }}" class="flex items-center justify-between px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition group">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-user-doctor w-5 text-center text-blue-400"></i> 
                            <span>{{ __('dashboard.sidebar.manage_providers') }}</span>
                        </div>
                        <span class="bg-blue-500/20 text-blue-400 text-[10px] px-2 py-0.5 rounded-full font-bold border border-blue-500/30">
                            {{ $stats['total_doctors'] ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('admin.users') }}" class="flex items-center justify-between px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition group">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-5 text-center text-indigo-400"></i> 
                            <span>{{ __('dashboard.sidebar.manage_users') }}</span>
                        </div>
                        <span class="bg-indigo-500/20 text-indigo-400 text-[10px] px-2 py-0.5 rounded-full font-bold border border-indigo-500/30">
                             {{ $stats['total_donors'] ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('admin.appointments.index') }}" class="flex items-center justify-between px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition group">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-calendar-check w-5 text-center text-yellow-400"></i> 
                            <span>{{ __('dashboard.sidebar.appointments') }}</span>
                        </div>
                        <span class="bg-yellow-500/20 text-yellow-400 text-[10px] px-2 py-0.5 rounded-full font-bold border border-yellow-500/30">
                            {{ $stats['pending_appts'] ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('admin.feedback.index') }}" class="flex items-center justify-between px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition group">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-comment-dots w-5 text-center text-pink-400"></i> 
                            <span>{{ __('dashboard.sidebar.user_feedback') }}</span>
                        </div>
                        <span class="bg-pink-500/20 text-pink-400 text-[10px] px-2 py-0.5 rounded-full font-bold border border-pink-500/30">
                            {{ $stats['pending_feedback'] ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition group">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-green-400"></i> 
                        <span>{{ __('dashboard.sidebar.system_reports') }}</span>
                    </a>
                @endif

                <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-8 mb-2">{{ __('dashboard.sidebar.account') }}</p>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-[#3E4F5F] rounded-lg transition {{ request()->routeIs('profile.edit') ? 'bg-[#3E4F5F] text-white' : '' }}">
                    <i class="fa-solid fa-user-gear w-5 text-center text-gray-400"></i> {{ __('dashboard.sidebar.profile_settings') }}
                </a>
            </nav>
        </aside>
        @endauth

        {{-- 3. Content Area --}}
        <div class="flex-1 @auth md:ml-64 @endauth flex flex-col min-w-0">
            <header class="bg-white h-16 flex items-center justify-between px-4 md:px-8 border-b border-gray-200 sticky top-0 z-40 shadow-sm">
                <div class="flex items-center gap-4">
                    {{-- Toggle Button for Mobile --}}
                    <button @click="sidebarOpen = true" class="md:hidden text-gray-600 p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2 class="font-bold text-gray-800 tracking-tight text-lg truncate">{{ $header ?? 'Dashboard' }}</h2>

                    {{-- LANGUAGE SWITCHER --}}
                    <div class="flex items-center bg-gray-100 p-1 rounded-lg ml-4">
                        <a href="{{ route('lang.switch', 'en') }}" 
                           class="px-3 py-1 text-[10px] font-bold rounded {{ app()->getLocale() == 'en' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            ENG
                        </a>
                        <a href="{{ route('lang.switch', 'rw') }}" 
                           class="px-3 py-1 text-[10px] font-bold rounded {{ app()->getLocale() == 'rw' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            KINY
                        </a>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 md:gap-5">
                    @auth
                        {{-- Notification Button --}}
                        <div class="relative group cursor-pointer p-2 hover:bg-gray-50 rounded-full transition">
                            <i class="fa-solid fa-bell text-gray-500 text-xl"></i>
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                        </div>

                        <div class="hidden sm:block h-6 w-[1px] bg-gray-200 mx-2"></div>

                        <span class="hidden lg:block text-[10px] font-black text-gray-400 tracking-widest uppercase">
                            {{ now()->format('l, d M Y') }}
                        </span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="bg-red-50 text-red-600 px-3 md:px-4 py-2 rounded-lg text-[10px] md:text-xs font-bold hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                {{ __('dashboard.sidebar.logout') }}
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="bg-red-500 text-white p-4 text-sm font-bold text-center">
                    {{ session('error') }}
                </div>
            @endif

            <main class="p-4 md:p-8 bg-[#F7F7F7] min-h-[calc(100vh-64px)] overflow-x-hidden">
                {{ $slot }}
            </main>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3E4F5F; border-radius: 10px; }
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>