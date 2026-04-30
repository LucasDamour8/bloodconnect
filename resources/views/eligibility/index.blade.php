<x-app-layout>
    <div class="py-8 bg-[#F7F7F7]">
        <div class="max-w-4xl mx-auto px-4">
            
            {{-- Article Header --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8">
                <span class="bg-red-100 text-red-600 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                    {{ __('eligibility.badge') }}
                </span>
                <h1 class="text-3xl font-black text-[#2A3F54] mt-4 mb-4 uppercase tracking-tight">
                    {{ __('eligibility.hero_title') }}
                </h1>
                <p class="text-lg text-gray-600 leading-relaxed">
                    {{ __('eligibility.hero_desc') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Who Can Donate --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500">
                    <h3 class="font-black text-[#2A3F54] uppercase tracking-widest text-sm mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-green-500"></i> {{ __('eligibility.can_donate_title') }}
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-600 font-bold">
                        <li>• {{ __('eligibility.can_donate_list.age') }}</li>
                        <li>• {{ __('eligibility.can_donate_list.weight') }}</li>
                        <li>• {{ __('eligibility.can_donate_list.health') }}</li>
                        <li>• {{ __('eligibility.can_donate_list.infection') }}</li>
                    </ul>
                </div>

                {{-- Who Should Wait --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-yellow-500">
                    <h3 class="font-black text-[#2A3F54] uppercase tracking-widest text-sm mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-yellow-500"></i> {{ __('eligibility.should_wait_title') }}
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li>• {{ __('eligibility.should_wait_list.illness') }}</li>
                        <li>• {{ __('eligibility.should_wait_list.surgery') }}</li>
                        <li>• {{ __('eligibility.should_wait_list.tattoo') }}</li>
                        <li>• {{ __('eligibility.should_wait_list.meds') }}</li>
                    </ul>
                </div>

                {{-- The Process --}}
                <div class="md:col-span-2 bg-[#2A3F54] text-white p-8 rounded-3xl shadow-xl">
                    <h3 class="font-black uppercase tracking-widest text-sm mb-6 text-red-400">
                        {{ __('eligibility.process_title') }}
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-black mb-2">01</div>
                            <p class="text-xs text-gray-300 uppercase">{{ __('eligibility.step_1') }}</p>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-black mb-2">02</div>
                            <p class="text-xs text-gray-300 uppercase">{{ __('eligibility.step_2') }}</p>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-black mb-2">03</div>
                            <p class="text-xs text-gray-300 uppercase">{{ __('eligibility.step_3') }}</p>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-black mb-2">04</div>
                            <p class="text-xs text-gray-300 uppercase">{{ __('eligibility.step_4') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Safety & Prep --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h3 class="font-black text-[#2A3F54] uppercase tracking-widest text-sm mb-4">
                        {{ __('eligibility.safety_title') }}
                    </h3>
                    <p class="text-xs text-gray-500 leading-loose">
                        {{ __('eligibility.safety_desc') }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h3 class="font-black text-[#2A3F54] uppercase tracking-widest text-sm mb-4">
                        {{ __('eligibility.prep_title') }}
                    </h3>
                    <ul class="text-xs text-gray-500 space-y-2">
                        <li><i class="fa-solid fa-utensils mr-2"></i> {{ __('eligibility.prep_meal') }}</li>
                        <li><i class="fa-solid fa-glass-water mr-2"></i> {{ __('eligibility.prep_water') }}</li>
                        <li><i class="fa-solid fa-id-card mr-2"></i> {{ __('eligibility.prep_id') }}</li>
                    </ul>
                </div>
            </div>

            {{-- Call to Action --}}
            <div class="mt-8 bg-red-600 p-8 rounded-3xl text-center text-white shadow-lg shadow-red-200">
                <h2 class="text-xl font-black uppercase tracking-widest mb-2">
                    {{ __('eligibility.cta_title') }}
                </h2>
                <p class="mb-6 opacity-90">{{ __('eligibility.cta_desc') }}</p>
                <a href="{{ route('donor.locations') }}" class="inline-block bg-white text-red-600 px-8 py-3 rounded-xl font-black uppercase tracking-widest text-xs hover:bg-gray-100 transition-all">
                    {{ __('eligibility.cta_btn') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>