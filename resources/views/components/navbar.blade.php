<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">

      {{-- LOGO --}}
      <a href="{{ url('/') }}"
         @click.prevent="
            if (window.dispatchEvent) {
                window.dispatchEvent(new CustomEvent('change-page', { detail: 'home' }));
            }
         "
         class="flex items-center gap-2 font-bold text-xl text-gray-900">

        <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>

        BloodConnect
      </a>

      {{-- Desktop Menu --}}
      <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">

        {{-- SPA SAFE BUTTONS --}}
        <button type="button"
            @click="$dispatch('change-page','donate')"
            class="hover:text-red-600 transition">
          {{ __('navigation.donate') }}
        </button>

        <button type="button"
            @click="$dispatch('change-page','locations')"
            class="hover:text-red-600 transition">
          {{ __('navigation.locations') }}
        </button>

        <button type="button"
            @click="$dispatch('change-page','learn')"
            class="hover:text-red-600 transition">
          {{ __('navigation.learn') }}
        </button>

      </div>

      <div class="flex items-center gap-3">

        {{-- Language --}}
        <div class="hidden sm:block">
            <a href="{{ route('lang.switch', App::getLocale() === 'en' ? 'rw' : 'en') }}"
               class="flex items-center gap-1 text-sm border border-gray-300 rounded-full px-3 py-1.5 hover:bg-gray-50 transition">
              {{ App::getLocale() === 'en' ? 'Kinyarwanda' : 'English' }}
            </a>
        </div>

        {{-- AUTH (IMPORTANT FIX: NORMAL LINKS, NOT SPA) --}}
        <div class="hidden md:flex items-center gap-3">
            @auth
              <a href="{{ route('dashboard') }}"
                 class="text-sm font-semibold text-gray-700 hover:text-red-600 transition">
                {{ __('navigation.dashboard') }}
              </a>

              <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button class="text-sm text-gray-500 hover:text-red-500 transition" type="submit">
                  {{ __('navigation.logout') }}
                </button>
              </form>
            @else
              <a href="{{ route('login') }}"
                 class="text-sm font-medium text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                {{ __('navigation.login') }}
              </a>

              <a href="{{ route('register') }}"
                 class="text-sm font-medium text-white bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700 transition">
                {{ __('navigation.signup') }}
              </a>
            @endauth
        </div>

        {{-- Mobile Button --}}
        <button @click="open = !open"
            class="md:hidden p-2 rounded-md text-gray-400 hover:text-red-600 hover:bg-gray-100">
            ☰
        </button>

      </div>
    </div>
  </div>

  {{-- MOBILE MENU --}}
  <div x-show="open"
       @click.away="open = false"
       class="md:hidden bg-white border-t border-gray-100">

    <div class="px-4 py-3 space-y-2">

        <button type="button"
            @click="$dispatch('change-page','donate'); open = false"
            class="block w-full text-left px-3 py-2 hover:text-red-600">
            {{ __('navigation.donate') }}
        </button>

        <button type="button"
            @click="$dispatch('change-page','locations'); open = false"
            class="block w-full text-left px-3 py-2 hover:text-red-600">
            {{ __('navigation.locations') }}
        </button>

        <button type="button"
            @click="$dispatch('change-page','learn'); open = false"
            class="block w-full text-left px-3 py-2 hover:text-red-600">
            {{ __('navigation.learn') }}
        </button>

        <hr>

        @auth
            <a href="{{ route('dashboard') }}" class="block px-3 py-2">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="block px-3 py-2 text-red-500">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block px-3 py-2">Login</a>
            <a href="{{ route('register') }}" class="block px-3 py-2 text-red-600 font-bold">Signup</a>
        @endauth

    </div>
  </div>
</nav>