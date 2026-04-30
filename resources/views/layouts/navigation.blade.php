{{-- resources/views/navigation.blade.php --}}
<nav x-data="{ mobileOpen: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Brand Logo --}}
            <div class="flex items-center gap-3">
                <a href="#" @click.prevent="$dispatch('change-page', 'home'); mobileOpen = false"
                   class="flex items-center gap-2 group">
                    <div class="h-9 w-9 rounded-full bg-red-600 flex items-center justify-center text-white font-bold shadow-sm transition group-hover:scale-105">
                        B
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-gray-900">
                        Blood<span class="text-red-600">Connect</span>
                    </span>
                </a>

                {{-- Language Switcher --}}
                <div class="ms-4 hidden md:flex items-center bg-gray-50 p-1 rounded-xl border border-gray-100">
                    <a href="{{ route('locale', 'en') }}"
                       class="px-3 py-1 text-[10px] font-black rounded-lg transition {{ app()->getLocale() == 'en' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                        EN
                    </a>
                    <a href="{{ route('locale', 'rw') }}"
                       class="px-3 py-1 text-[10px] font-black rounded-lg transition {{ app()->getLocale() == 'rw' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                        RW
                    </a>
                </div>
            </div>

            {{-- Desktop Navigation Links --}}
            <div class="hidden sm:flex sm:items-center sm:gap-6">
                <button @click="$dispatch('change-page', 'home')"
                        class="text-sm font-bold pb-0.5 transition text-gray-500 hover:text-red-600">
                    {{ __('navigation.dashboard') }}
                </button>
                <button @click="$dispatch('change-page', 'learn')"
                        class="text-sm font-bold pb-0.5 transition text-gray-500 hover:text-red-600">
                    {{ __('Learn') }}
                </button>
                <button @click="$dispatch('change-page', 'donate')"
                        class="text-sm font-bold pb-0.5 transition text-gray-500 hover:text-red-600">
                    {{ __('Donate') }}
                </button>
                <button @click="$dispatch('change-page', 'locations')"
                        class="px-5 py-2 bg-red-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition shadow-md shadow-red-100">
                    {{ __('Donation Centers') }}
                </button>

                @auth
                {{-- User Dropdown --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg text-gray-600 bg-white hover:text-gray-900 hover:bg-gray-50 focus:outline-none transition border border-transparent">
                            {{ Auth::user()->full_name }}
                            <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('navigation.profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('navigation.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="mobileOpen = !mobileOpen"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': mobileOpen, 'hidden': !mobileOpen}" class="hidden sm:hidden" x-cloak>
        <div class="pt-2 pb-3 space-y-1 border-t border-gray-100">
            <button @click="$dispatch('change-page', 'home'); mobileOpen = false"
                    class="block w-full text-left px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 transition">
                {{ __('navigation.dashboard') }}
            </button>
            <button @click="$dispatch('change-page', 'learn'); mobileOpen = false"
                    class="block w-full text-left px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 transition">
                {{ __('Learn') }}
            </button>
            <button @click="$dispatch('change-page', 'donate'); mobileOpen = false"
                    class="block w-full text-left px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 transition">
                {{ __('Donate') }}
            </button>
            <button @click="$dispatch('change-page', 'locations'); mobileOpen = false"
                    class="block w-full text-left px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 transition">
                {{ __('Donation Centers') }}
            </button>
        </div>

        @auth
        {{-- Mobile User Info --}}
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-red-400 flex items-center justify-center text-white font-bold">
                    {{ substr(Auth::user()->full_name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-base text-gray-800">{{ Auth::user()->full_name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('navigation.profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('navigation.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>