<div class="space-y-10">

    {{-- Header --}}
    <div class="text-center">
        <h1 class="text-4xl font-black text-gray-900">Donation Centers</h1>
        <p class="text-gray-500 mt-2 text-lg">
            Find a verified blood bank near your location.
        </p>
    </div>

    {{-- Grid --}}
    <div class="grid md:grid-cols-3 gap-8">

        @forelse($locations as $location)
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition group">

            {{-- Icon --}}
            <div class="flex justify-between items-start mb-6">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                </div>

                @if($location->availability == 'high')
                    <span class="bg-red-100 text-red-700 text-[10px] font-black px-3 py-1 rounded-full uppercase">
                        Urgent Need
                    </span>
                @endif
            </div>

            {{-- Info --}}
            <h3 class="text-xl font-extrabold mb-2">{{ $location->name }}</h3>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">
                {{ $location->address }}
            </p>

            <div class="flex items-center gap-3 text-sm font-bold text-gray-400 mb-8">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $location->hours }}
            </div>

            {{-- BOOK NOW (redirect to login) --}}
            <a href="{{ route('login') }}"
               class="block w-full py-4 bg-gray-900 text-white text-center font-bold rounded-2xl hover:bg-red-600 transition shadow-lg">
                Book Now
            </a>

        </div>
        @empty
        <div class="col-span-3 text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
            <p class="text-gray-400 font-bold italic">
                No centers found in your area.
            </p>
        </div>
        @endforelse

    </div>

</div>