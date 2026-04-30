@extends('layouts.guest')
@section('title', __('home.title'))
@section('content')

{{-- Manage the state for the whole page --}}
<div x-data="{ currentPage: 'home' }" 
     @change-page.window="currentPage = $event.detail">

    <div x-show="currentPage === 'home'">
        {{-- Hero Section --}}
        <section class="bg-gradient-to-br from-red-50 to-white py-24 text-center">
            <div class="max-w-4xl mx-auto px-4">
                <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-4">
                    {{ __('home.hero_line1') }}<br>
                    <span class="text-red-600">{{ __('home.hero_line2') }}</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-10">
                    {{ __('home.hero_subtitle') }}
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    {{-- Click triggers the Donate view --}}
                    <button @click="currentPage = 'donate'"
                        class="bg-red-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-red-700 transition shadow-md">
                        {{ __('home.cta_check') }}
                    </button>
                    {{-- Click triggers the Locations view --}}
                    <button @click="currentPage = 'locations'"
                        class="border-2 border-gray-800 text-gray-800 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-100 transition">
                        {{ __('home.cta_locations') }}
                    </button>
                </div>
            </div>
        </section>

        {{-- Stats Row --}}
        <section class="bg-white py-16 border-y border-gray-100">
            <div class="max-w-6xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach([['50K+','home.stat_lives'],['15K+','home.stat_donors'],['200+','home.stat_centers'],['24/7','home.stat_support']] as [$num, $label])
                <div>
                    <p class="text-4xl font-extrabold text-red-600">{{ $num }}</p>
                    <p class="text-gray-500 mt-1">{{ __($label) }}</p>
                </div>
                @endforeach
            </div>
        </section>

        {{-- Feature Cards --}}
        <section class="bg-red-50/50 py-20">
            <div class="max-w-6xl mx-auto px-4">
                <div class="grid md:grid-cols-3 gap-6">
                    {{-- Manual Button for Learn Card --}}
                    <div @click="currentPage = 'learn'" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md cursor-pointer transition border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="text-3xl mb-4">📖</div>
                            <h3 class="font-bold text-gray-900 text-lg mb-2">{{ __('home.feature_learn') }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed mb-6">{{ __('home.feature_learn_desc') }}</p>
                        </div>
                        <span class="text-red-600 font-bold text-sm hover:underline">Learn More →</span>
                    </div>

                    {{-- Other Cards --}}
                    @foreach([
                        ['📍','home.feature_locations','home.feature_locations_desc', 'locations'],
                        ['❤️','home.feature_health','home.feature_health_desc', 'eligibility'],
                    ] as [$icon,$title,$desc, $pageName])
                    <div @click="currentPage = '{{ $pageName }}'" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md cursor-pointer transition border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="text-3xl mb-4">{{ $icon }}</div>
                            <h3 class="font-bold text-gray-900 text-lg mb-2">{{ __($title) }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed mb-6">{{ __($desc) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <div x-show="currentPage === 'learn'" x-cloak>
        @include('learn.content')
    </div>

    <div x-show="currentPage === 'donate'" x-cloak>
        @include('learn.donatecontent')
    </div>

    <div x-show="currentPage === 'eligibility'" x-cloak>
        @include('learn.eligibilitycontents')
    </div>

    <div x-show="currentPage === 'locations'" x-cloak>
        @include('learn.locationcontents')
    </div>

</div>

@endsection