@extends('layouts.guest')
@section('title', __('home.title'))
@section('content')

{{-- 
    1. 'currentPage' tracks the view.
    2. 'x-init' sets up a watcher to scroll to the top whenever the page changes.
    3. 'x-on:change-page.window' allows your navigation.blade.php to control this page.
--}}
<div x-data="{ 
    showEligibility: false, 
    showTracking: {{ isset($tracked_appointment) ? 'true' : 'false' }},
    currentPage: 'home' 
}"
x-init="$watch('currentPage', value => window.scrollTo({top: 0, behavior: 'smooth'}))"
x-on:change-page.window="currentPage = $event.detail">

    {{-- VIEW: HOME --}}
    <div x-show="currentPage === 'home'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4">
        {{-- Hero Section --}}
        <section class="bg-gradient-to-br from-red-50 to-white py-16 md:py-24 text-center">
            <div class="max-w-4xl mx-auto px-4">
                @if(session('error'))
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative animate-bounce">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative">
                        {{ session('success') }}
                    </div>
                @endif

                <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-4">
                    {{ __('home.hero_line1') }}<br>
                    <span class="text-red-600">{{ __('home.hero_line2') }}</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-10">
                    {{ __('home.hero_subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button @click="showEligibility = true"
                        class="bg-red-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-red-700 transition shadow-md">
                        {{ __('home.btn_eligibility') }}
                    </button>
                    
                    <a href="#announcements"
                        class="bg-white border-2 border-gray-200 text-gray-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-50 transition">
                        {{ __('home.btn_announcements') }}
                    </a>

                    <a href="{{ route('login') }}"
                        class="border-2 border-red-600 text-red-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-red-50 transition">
                        {{ __('home.btn_book') }}
                    </a>
                </div>
            </div>
        </section>

        {{-- Announcements Section --}}
        <section id="announcements" class="bg-white py-12">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('home.latest_updates') }}</h2>
                    <div class="h-1 flex-1 mx-4 bg-gray-100 rounded-full"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($announcements as $news)
                    <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-red-200 transition">
                        <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest">{{ $news->created_at->format('d M, Y') }}</span>
                        <h3 class="font-bold text-gray-800 mt-2">{{ $news->title }}</h3>
                        <p class="text-gray-500 text-sm mt-2 line-clamp-3">{{ $news->content }}</p>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-10">
                        <p class="text-gray-400 italic text-lg">{{ __('home.no_announcements') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Tracking Section --}}
        <section class="bg-red-600 py-12 text-white">
            <div class="max-w-4xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-bold">{{ __('home.tracking_title') }}</h3>
                    <p class="text-red-100 text-sm">{{ __('home.tracking_subtitle') }}</p>
                </div>
                <button @click="showTracking = true" class="bg-white text-red-600 hover:bg-gray-100 px-8 py-3 rounded-xl font-bold transition shadow-lg">
                    {{ __('home.btn_track') }}
                </button>
            </div>
        </section>

        {{-- Feature Cards --}}
        <section class="bg-gray-50 py-20">
            <div class="max-w-6xl mx-auto px-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach([
                        ['📍','home.feature_locations','home.feature_locations_desc'],
                        ['📅','home.feature_schedule','home.feature_schedule_desc'],
                        ['❤️','home.feature_health','home.feature_health_desc']
                    ] as [$icon,$title,$desc])
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 transition-all">
                        <div class="text-3xl mb-4">{{ $icon }}</div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">{{ __($title) }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ __($desc) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Reach Us Section --}}
        <section class="bg-white py-20 border-t">
            <div class="max-w-6xl mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ __('home.reach_us') }}</h2>
                        <p class="text-gray-600 mb-8">{{ __('home.reach_us_subtitle') }}</p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 text-gray-700">
                                <span class="w-10 h-10 bg-red-50 text-red-600 rounded-full flex items-center justify-center">📍</span>
                                <span>{{ __('home.location_city') }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-gray-700">
                                <span class="w-10 h-10 bg-red-50 text-red-600 rounded-full flex items-center justify-center">📧</span>
                                <span>support@bloodconnect.rw</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('feedback.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="text" name="name" placeholder="{{ __('home.form_name') }}" required
                            class="w-full p-4 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-red-400/20 text-sm">
                        <input type="email" name="email" placeholder="{{ __('home.form_email') }}" required
                            class="w-full p-4 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-red-400/20 text-sm">
                        <textarea name="message" rows="4" placeholder="{{ __('home.form_message') }}" required
                            class="w-full p-4 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-red-400/20 text-sm"></textarea>
                        <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-xl font-bold hover:bg-black transition shadow-lg">
                            {{ __('home.btn_send') }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>

    {{-- VIEW: DONATE --}}
    <div x-show="currentPage === 'donate'" x-cloak x-transition:enter="transition ease-out duration-300">
        <div class="bg-white min-h-screen">
             <div class="max-w-6xl mx-auto px-4 py-12">
                <button @click="currentPage = 'home'" class="mb-8 flex items-center gap-2 text-red-600 font-bold hover:translate-x-[-4px] transition-transform">
                    <span>←</span> {{ __('home.back_to_home') ?? 'Inyuma' }}
                </button>
                @include('donate') {{-- Changed from learn.donatecontent --}}
             </div>
        </div>
    </div>

    {{-- VIEW: LOCATIONS --}}
    <div x-show="currentPage === 'locations'" x-cloak x-transition:enter="transition ease-out duration-300">
        <div class="bg-gray-50 min-h-screen">
            <div class="max-w-6xl mx-auto px-4 py-12">
                <button @click="currentPage = 'home'" class="mb-8 flex items-center gap-2 text-red-600 font-bold hover:translate-x-[-4px] transition-transform">
                    <span>←</span> {{ __('home.back_to_home') ?? 'Inyuma' }}
                </button>
                @include('homelocation') {{-- Changed from learn.locationcontents --}}
            </div>
        </div>
    </div>

    {{-- VIEW: LEARN --}}
    <div x-show="currentPage === 'learn'" x-cloak x-transition:enter="transition ease-out duration-300">
        <div class="bg-white min-h-screen">
            <div class="max-w-6xl mx-auto px-4 py-12">
                <button @click="currentPage = 'home'" class="mb-8 flex items-center gap-2 text-red-600 font-bold hover:translate-x-[-4px] transition-transform">
                    <span>←</span> {{ __('home.back_to_home') ?? 'Inyuma' }}
                </button>
                @include('learn') {{-- Changed from learn.content --}}
            </div>
        </div>
    </div>

    {{-- MODAL: Eligibility --}}
    <div x-show="showEligibility" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-md" x-cloak>
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden" @click.away="showEligibility = false">
            <div class="p-6 border-b bg-red-600 text-white flex justify-between items-center">
                <h2 class="text-2xl font-bold">{{ __('home.eligibility_modal_title') }}</h2>
                <button @click="showEligibility = false" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>
            
            <div class="p-8 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach(__('home.eligibility_list') as $index => $req)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="bg-red-100 text-red-600 font-bold text-xs w-6 h-6 flex items-center justify-center rounded-full shrink-0">
                            {{ $index + 1 }}
                        </span>
                        <span class="text-gray-700 text-sm font-medium">{{ $req }}</span>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 p-4 bg-blue-50 rounded-2xl text-blue-800 text-xs flex gap-3 italic">
                    <p>{{ __('home.eligibility_note') }}</p>
                </div>
            </div>

            <div class="p-6 border-t bg-gray-50">
                <button @click="showEligibility = false" class="w-full bg-gray-900 text-white py-3 rounded-xl font-bold hover:bg-black transition">
                    {{ __('home.btn_understand') }}
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL: Track --}}
    <div x-show="showTracking" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-md" x-cloak>
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl relative" @click.away="showTracking = false">
            <button @click="showTracking = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800">&times;</button>
            
            <h2 class="text-2xl font-bold mb-2">{{ __('home.track_title') }}</h2>
            <p class="text-sm text-gray-500 mb-6">{{ __('home.track_desc') }}</p>
            
            <form action="{{ route('appointments.track') }}" method="GET">
                <input type="text" name="appointment_id" placeholder="BC-12345" required
                    class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-red-500 mb-4 text-center font-mono text-xl uppercase tracking-widest">
                
                @if(isset($tracked_appointment))
                    <div class="mb-6 p-4 bg-green-50 rounded-2xl border border-green-100">
                        <p class="text-xs text-green-600 font-bold uppercase tracking-wider mb-2 text-center">{{ __('home.current_status') }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">{{ __('home.status_label') }}:</span>
                            <span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold">{{ strtoupper($tracked_appointment->status) }}</span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-gray-600 font-medium">{{ __('home.date_label') }}:</span>
                            <span class="text-gray-900 font-bold">{{ \Carbon\Carbon::parse($tracked_appointment->appointment_date)->format('d M, Y') }}</span>
                        </div>
                    </div>
                @endif

                <div class="flex gap-3">
                    <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition">
                        {{ __('home.btn_find') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection