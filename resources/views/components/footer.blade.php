{{-- Footer adapts automatically based on App::getLocale() --}}
<footer x-data>

    {{-- CTA Banner --}}
    <section class="bg-red-600 py-20 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
            {{ __('footer.cta_title') }}
        </h2>
        <p class="text-red-100 mb-8 text-lg">
            {{ __('footer.cta_subtitle') }}
        </p>

        {{-- SPA BUTTON --}}
        <button
            @click="$dispatch('change-page','donate')"
            class="bg-white text-gray-900 px-10 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition inline-block">
            {{ __('footer.cta_button') }}
        </button>
    </section>

    {{-- Footer body --}}
    <div class="bg-gray-900 text-gray-300 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">

                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="text-white font-bold text-lg">BloodConnect</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        {{ __('footer.brand_desc') }}
                    </p>
                </div>

                {{-- Quick Links (SPA FIXED) --}}
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('footer.quick_links') }}</h4>
                    <ul class="space-y-2 text-sm">

                        <li>
                            <button @click="$dispatch('change-page','donate')"
                                class="hover:text-white transition text-left">
                                {{ __('footer.link_donate') }}
                            </button>
                        </li>

                        <li>
                            <button @click="$dispatch('change-page','locations')"
                                class="hover:text-white transition text-left">
                                {{ __('footer.link_centers') }}
                            </button>
                        </li>

                        <li>
                            <button @click="$dispatch('change-page','donate')"
                                class="hover:text-white transition text-left">
                                {{ __('footer.link_eligibility') }}
                            </button>
                        </li>

                    </ul>
                </div>

                {{-- Support --}}
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('footer.support') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">{{ __('footer.help_center') }}</a></li>
                        <li><a href="#" class="hover:text-white transition">{{ __('footer.contact_us') }}</a></li>
                        <li><a href="#" class="hover:text-white transition">{{ __('footer.faq') }}</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('footer.legal') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">{{ __('footer.privacy_policy') }}</a></li>
                        <li><a href="#" class="hover:text-white transition">{{ __('footer.terms') }}</a></li>
                        <li><a href="#" class="hover:text-white transition">{{ __('footer.donor_eligibility_policy') }}</a></li>
                        <li><a href="#" class="hover:text-white transition">{{ __('footer.blood_safety') }}</a></li>
                        <li><a href="#" class="hover:text-white transition">{{ __('footer.donor_rights') }}</a></li>
                    </ul>
                </div>

            </div>

            {{-- Bottom bar --}}
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} BloodConnect Rwanda. All rights reserved.
            </div>
        </div>
    </div>

</footer>