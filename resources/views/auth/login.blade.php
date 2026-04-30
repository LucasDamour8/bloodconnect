@extends('layouts.guest')
@section('title', __('auth.btn_login'))

@section('content')
<div class="min-h-screen bg-red-50/40 flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                  {{-- GO HOME BUTTON --}}
<div class="flex justify-end mb-4">
    <a href="{{ route('home') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-full
              bg-red-50 text-red-600 font-semibold text-sm
              border border-red-100
              hover:bg-red-600 hover:text-white
              hover:shadow-md
              transition-all duration-200">
        {{ __('auth.back_home') }}
    </a>
</div>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">{{ __('auth.login_title') }}</h2>
            <p class="mt-2 text-sm text-gray-500">{{ __('auth.login_subtitle') }}</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm border border-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="loginForm" class="mt-8 space-y-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('auth.email') }}</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" 
                        class="appearance-none rounded-xl relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">{{ __('auth.password') }}</label>
                    <input id="password" name="password" type="password" required 
                        class="appearance-none rounded-xl relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">{{ __('auth.remember_me') }}</label>
                </div>
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-red-600 hover:text-red-500">{{ __('auth.forgot_password') }}</a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-red-600 hover:bg-red-700 transition">
                    {{ __('auth.btn_login') }}
                </button>
                <button type="reset" class="w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition">
                    {{ __('auth.btn_clear') }}
                </button>
            </div>
        </form>

        <p class="text-center text-sm text-gray-600">
            {{ __('auth.no_account') }} 
            <a href="{{ route('register') }}" class="font-bold text-red-600 hover:text-red-500">{{ __('auth.btn_register_now') }}</a>
        </p>
    </div>
</div>
@endsection