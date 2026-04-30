@extends('layouts.guest')
@section('title', __('auth.register_title'))

@section('content')
<div class="min-h-screen bg-red-50/40 flex flex-col items-center justify-center p-4 py-12">

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 w-full max-w-2xl p-8 md:p-12">

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

        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">{{ __('auth.register_title') }}</h2>
            <p class="text-gray-500 mt-2">{{ __('auth.register_subtitle') }}</p>
        </div>

        {{-- ✅ ERROR DISPLAY FIX (IMPORTANT) --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.first_name') }}</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.last_name') }}</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.national_id') }}</label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" required maxlength="16"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.dob') }}</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6"
                 x-data="{ role: '{{ old('role', 'donor') }}' }">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.register_as') }}</label>
                    <select name="role" x-model="role" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                        <option value="donor">{{ __('auth.role_donor') }}</option>
                        <option value="doctor">{{ __('auth.role_doctor') }}</option>
                    </select>
                </div>

                <div x-show="role === 'donor'">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.blood_type') }}</label>
                    <select name="blood_type"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                        <option value="">{{ __('auth.unknown') }}</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                            <option value="{{ $type }}" {{ old('blood_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.gender') }}</label>
                    <select name="gender" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('auth.male') }}</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('auth.female') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.district') }}</label>
                    <input type="text" name="district" value="{{ old('district') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.sector') }}</label>
                    <input type="text" name="sector" value="{{ old('sector') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.password') }}</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            <div class="flex items-start gap-3">
                <input type="checkbox" name="terms" id="terms" required class="mt-1 accent-red-600">
                <label for="terms" class="text-xs text-gray-600 leading-relaxed">
                    {{ __('auth.terms_text') }}
                    <a href="#" class="text-red-600 hover:underline">{{ __('auth.terms_link') }}</a>
                    {{ __('auth.terms_and_confirm') }}
                </label>
            </div>

            <button type="submit"
                class="w-full bg-red-600 text-white py-4 rounded-2xl font-bold hover:bg-red-700 transition shadow-lg">
                {{ __('auth.btn_register') }}
            </button>

            <p class="text-center text-sm text-gray-600">
                {{ __('auth.has_account') }}
                <a href="{{ route('login') }}" class="text-red-600 font-bold hover:underline">
                    {{ __('auth.btn_login_link') }}
                </a>
            </p>
        </form>
    </div>
</div>
@endsection